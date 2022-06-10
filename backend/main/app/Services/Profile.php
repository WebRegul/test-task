<?php

namespace App\Services;

use App\Exceptions\ForbiddenException;
use App\Facades\Member;
use App\Helpers\Permissions;
use App\Models\Image;
use App\Models\Profile as ProfileModel;
use App\Models\Tariff;
use App\Services\Billing\BillingService;
use App\Services\Images\Image as ImagesImage;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Collection;

/**
 * Class Profile
 * @package App\Services
 */
class Profile
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var ProfileModel
     */
    private ProfileModel $profile;

    /**
     * временное решение, чтобы прокинуть защищенные поля в мембера, но не выбросить их на всеобщее обозрение
     * @todo переписать в целом логику базовую сборки данных, убрать излишнюю централизацию, вызывает слишком большую связанность,
     * @todo убивает гибкость и приводить к систематически вылетающим багам с тем что либо что-то не то проскочило в паблик, либо наоборот пропало из кабинета
     * @todo да и с точки зрения нагрузки выбирать во всех запросах полный набор данных со всеми реляциями не есть гуд подход
     * @var array
     */
    public const MEMBER_FIELDS = [
        'id', 'name', 'surname', 'register_source_id',
        // 'tariff_id', 'tariff_period', 'tariff_finished_at', 'auto_renewal',
        //'source_data',
    ];

    /**
     * Profile constructor.
     * @param string|null $id
     * @param string|null $name
     */
    public function __construct(?string $id = null, ?string $name = null)
    {
        $this->set($id, $name);
    }

    /**
     * @param ProfileModel $profile
     */
    private function setByModel(ProfileModel $profile): Profile
    {
        $this->profile = $profile;
        $this->id = empty($this->profile->id) ? null : $this->profile->id;

        return $this;
    }

    /**
     * @param string|null $id
     * @param string|null $name
     */
    private function set(?string $id = null, ?string $name = null): Profile
    {
        //вот тут не стоит смешивать создание и получение. потенциалльный риск получить трудновыыловимые баги с размножением
        //надо разделить.
        $conditions = !empty($id) ? ['id' => $id] : ['name' => $name];
        $profile = ProfileModel::query()
                ->where($conditions)
                ->with('contacts')
                ->first() ?? new ProfileModel();

        $this->setByModel($profile);

        return $this;
    }

    /**
     * @param string|null $userId
     */
    public function setByUserId(?string $userId = null, ?bool $forMember = false): Profile
    {
        //аналогично предыдущему. если мы не получили профиль по ид, для  чего создавать новый? мы ведь явно по  конкретному  ид ищем
        //если предпоолагается именно поолучить или создать, то логично использовать firstOrCreate готовый.
        $model = ProfileModel::query();
        /**
         * @Annotation  быстрый костыль, чтобы дать мемберу недостающие поля
         * @todo уменьшить связанность. добавить нормальное разделение приват и паблик (можно даже пойти путем разделения классов или добавления декоратора)
         */
        if ($forMember) {
            $model = $model->select(static::MEMBER_FIELDS);
        }
        $profile = $model->where('user_id', $userId)
            ->with('contacts')
            ->firstOrNew();
        if ($forMember) {
            $profile->makeVisible(static::MEMBER_FIELDS);
        }

        $this->setByModel($profile);

        return $this;
    }

    /**
     * @return ProfileModel
     */
    public function get(): ProfileModel
    {
        return $this->profile;
    }

    /**
     * @param string $userId
     * @param array $data
     * @return Collection
     */
    public function create(string $userId, array $data = []): Collection
    {
        $data = collect($data);
        $this->setByUserId($userId);
        $profile = $this->get();
        $isNewProfile = empty($profile->id);

        $columns = $profile->getFillable();
        $profile->fill($data->only($columns)
            ->except('birthday_at')->toArray());

        if ($isNewProfile) {
            $registerSourceName = $data->get('source_name') ?? 'default';

            $profile->user_id = $userId;
            $profile->creator_id = $userId;
//            $profile->register_source_id = app(RegisterSource::class)
//                ->getIdByName($registerSourceName);
        }

        $profile->updater_id = $userId;
        $profile->name = $data->get('name');
        $profile->surname = $data->get('surname');

        $birthdayAt = $data->get('birthday_at', '');
        if (is_null($birthdayAt)) {
            $profile->birthday_at = null;
        } elseif (!empty($birthdayAt)) {
            $profile->birthday_at = Carbon::parse($birthdayAt)->toDateTimeString();
        }

        // $tarifff = Tariff::where('price_month', 0)->where('price_year', 0)->first();

        //$profile->tariff_id = $tarifff->id;
//        $profile->tariff_period = 'month';
//        $now = Carbon::now();
//        $profile->tariff_finished_at = $now->addMonth();
        $profile->save();

        if ($isNewProfile) {
            $profile->name = $profile->id;
            $profile->save();
        }

        $profile->refresh();

        $this->setByModel($profile);

        return collect($profile);
    }

    /**
     * @param array $data
     * @return Collection
     * @throws ForbiddenException
     */
    public function update(array $data): Collection
    {
        $profile = $this->get();

        if (!empty($data)) {
            $data = collect($data);

            if (!Permissions::isOwner($profile)) {
                throw new ForbiddenException('профиль принадлежит другому пользователю');
            }

            $columns = $profile->getFillable();
            $profile->fill($data->only($columns)
                ->except(['birthday_at', 'source_data'])
                ->toArray());

            $birthdayAt = $data->get('birthday_at', '');
            if (is_null($birthdayAt)) {
                $profile->birthday_at = null;
            } elseif (!empty($birthdayAt)) {
                $profile->birthday_at = Carbon::parse($birthdayAt)->toDateTimeString();
            }

            if (!empty($data->get('source_data'))) {
                $profile->source_data = $data->get('source_data');
            }

            $profile->save();
            $profile->refresh();

            $this->setByModel($profile);

            Member::set('profile', collect($this->get())->except('contacts'));
        }

        return collect($profile);
    }


    public function getProfilePhoto()
    {
        $photos = [];
        if (!empty($this->profile->id)) {
            $photos = (new ImagesImage())
                ->getEntityImages(
                    $this->profile->id,
                    'profile',
                    null,
                    null,
                    [
                        'crop_public_paths' => 'photos'
                    ],
                    [
                        'src_xl', 'src_gallery_mini', 'paths', 'public_paths',
                        'crop_paths', 'crop_public_paths', 'crop_bounds', 'rotate_angle',
                        'priority', 'transformations'
                    ]
                );
            $photos = collect($photos)->get(0);
        }
        return $photos;
    }
}
