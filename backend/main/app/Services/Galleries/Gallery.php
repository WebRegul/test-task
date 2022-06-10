<?php

namespace App\Services\Galleries;

use App\Exceptions\ForbiddenException;
use App\Exceptions\NotAcceptable;
use App\Helpers\Dto\DtoInterface;
use App\Helpers\Permissions;
use App\Jobs\DeleteGalleryJob;
use App\Models\Gallery as GalleryModel;
use App\Models\GallerySection as GallerySectionModel;
use App\Models\Image as ImageModel;
use App\Models\ImageSections as ImageSectionsModel;
use App\Services\Images\Image;
use App\Services\Profile;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

/**
 * Class Gallery
 * @package App\Services
 */
class Gallery
{
    /**
     * @var array
     */
    public const ACCESS_TOKEN_TYPES = [
        'show',
        'download'
    ];

    /**
     * @var string|null
     */
    private $id;

    /**
     * @var GalleryModel
     */
    private $gallery;

    /**
     * токен доступа к галерее
     * @var string
     */
    protected $accessToken = '';

    /**
     * флаг получения изображений внутри данных секций
     * @var bool
     */
    protected $withImages = false;

    /**
     * Gallery constructor.
     * @param string|null $id
     * @param string|null $name
     * @param string|null $userId
     */
    public function __construct(?string $id = null, ?string $name = null, ?string $userId = null)
    {
        $this->set($id, $name, $userId);
    }

    /**
     * Установка токена доступа к галерее
     * @param $token
     * @return Gallery
     */
    public function setAccessToken($token): Gallery
    {
        $this->accessToken = $token;
        return $this;
    }

    /**
     * Проверка доступа к галерее
     * @return boolean
     */
    protected function checkAccess(): bool
    {
        $gallery = collect($this->gallery);
        if ($gallery->get('is_secure')) {
            $token = new GalleryToken($this->id, 'show');
            if (!$this->accessToken || !$token->checkToken($this->accessToken)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Получение данных по галерее для веба. Времянка, позже оптимизировать после рефакторинга всего класса.
     * @param bool $withImages
     * @return Collection
     * @throws ForbiddenException
     */
    public function getWeb(bool $withImages = false): Collection
    {
        if (!$this->checkAccess()) {
            throw new ForbiddenException('Доступ к данной галерее возможен только по паролю');
        }

        if ($withImages) {
            $this->setWithImages($withImages)->set($this->getId());
        }

        return collect($this->get());
    }

    /**
     * @param bool $withImages
     * @return $this
     */
    public function setWithImages(bool $withImages): Gallery
    {
        $this->withImages = $withImages;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Получение не защищенных, открытых данных о галереи. Эти данные доступны даже у запароленных галерей.
     * @return Collection
     */
    public function getUnGuarded(): Collection
    {
        return collect($this->get())->only(['id', 'title', 'shooting_at', 'name']);
    }

    /**
     * @param string|null $id
     * @param string|null $name
     * @param string|null $userId
     * @return Gallery
     */
    private function set(?string $id = null, ?string $name = null, ?string $userId = null): Gallery
    {
        $conditions = !empty($id) ? ['id' => $id] : ['name' => $name, 'user_id' => $userId];
        $builder = GalleryModel::query()
            ->where($conditions)
            ->with(['sections', 'cover'])
            ->when($this->withImages, function (Builder $q) {
                $q->with(['sections.images']);
            });
        $this->gallery = $builder->firstOrNew();
        $this->id = empty($this->gallery->id) ? null : $this->gallery->id;

        return $this;
    }

    /**
     * @return GalleryModel
     */
    public function get(): GalleryModel
    {
        return $this->gallery;
    }

    protected function sortSectionsImages($sections): Collection
    {
        $sections = collect($sections)->recursive();
        return $sections->transform(function ($e) {
            $images = $e->get('images');
            if (!$images) {
                return $e;
            }
            switch ($e->get('order_mode')) {
                case 'manual':
                    $images = $images->sortBy('order');
                    break;
                case 'desc':
                    $images = $images->sortByDesc('filename');
                    break;
                case 'asc':
                    $images = $images->sortByDesc('asc');
                    break;
            }
            $e->put('images', $images->values()->all());
            return $e;
        });
    }

    /**
     * @param array $data
     * @return Collection
     */
    public function create(array $data): Collection
    {
        $data = collect($data);
        $section = null;

        DB::transaction(function () use ($data, &$section) {
            $gallery = new GalleryModel($data->except('shooting_at')->toArray());

            $gallery->user_id = $data->get('user_id');
            $gallery->is_secure = $data->get('is_secure');
            $gallery->password = $data->get('password') ?? '';
            $gallery->is_download_secure = $data->get('is_download_secure');
            $gallery->download_password = $data->get('download_password') ?? '';

            $shootingDate = $data->get('shooting_at', '');
            if (is_null($shootingDate)) {
                $gallery->shooting_at = null;
            } elseif (!empty($shootingDate)) {
                $gallery->shooting_at = Carbon::parse($shootingDate)->toDateTimeString();
            }

            $gallery->save();
            $gallery->refresh();

            if (empty($data->get('name'))) {
                $gallery->name = $gallery->id;
                $gallery->save();
                $gallery->refresh();
            }

            $this->set($gallery->id);

            $section = $this->createSection([
                'title' => $data->get(
                    'default_section_title',
                    config('gallery.sections.default_title')
                )
            ]);
        });

        $this->gallery->makeVisible(['password', 'download_password']);

        $result = collect($this->gallery);
        $result->put('sections', [$section]);

        return $result;
    }

    /**
     * @param array $data
     * @return Collection
     * @throws ForbiddenException
     */
    public function update(array $data): Collection
    {
        if (empty($this->id)) {
            throw new ItemNotFoundException('галерея не существует');
        }

        $gallery = $this->get();
        $data = collect($data);

        if (!Permissions::isOwner($gallery)) {
            throw new ForbiddenException('галерея принадлежит другому пользователю');
        }

        $columns = $gallery->getFillable();

        $gallery->fill($data->only($columns)
            ->except('shooting_at')
            ->toArray());

        $gallery->is_secure = $data->get('is_secure');
        $gallery->password = $data->get('password') ?? '';
        $gallery->is_download_secure = $data->get('is_download_secure');
        $gallery->download_password = $data->get('download_password') ?? '';

        $shootingDate = $data->get('shooting_at', '');
        if (is_null($shootingDate)) {
            $gallery->shooting_at = null;
        } elseif (!empty($shootingDate)) {
            $gallery->shooting_at = Carbon::parse($shootingDate)->toDateTimeString();
        }

        $gallery->save();

        $this->withImages = true;
        $this->set($gallery->id);

        $this->gallery->makeVisible(['password', 'download_password']);

        return collect($this->gallery);
    }

    /**
     * @throws ForbiddenException
     */
    public function delete(): void
    {
        if (!Permissions::isOwner($this->gallery)) {
            throw new ForbiddenException('галерея принадлежит другому пользователю');
        }

        dispatch_now(new DeleteGalleryJob($this->get()));
    }

    /**
     * @return Collection
     * @throws ItemNotFoundException|ForbiddenException
     */
    public function getFullInfo(): Collection
    {
        $this->setWithImages(true)->set($this->getId());
        $gallery = $this->get();

        if (empty($gallery)) {
            throw new ItemNotFoundException('галерея не существует');
        }

        if (!Permissions::isOwner($gallery)) {
            throw new ForbiddenException('галерея принадлежит другому пользователю');
        }

        $gallery->makeVisible(['password', 'download_password']);

        //сортируем изображения
        $result = collect($gallery);
        $result->put('sections', $this->sortSectionsImages($result->get('sections')));

        return $result;
    }

    /**
     * @return Collection
     */
    public function getGalleryCover(): Collection
    {
        $gallery = $this->get();
        $cover = collect($gallery)->only('cover');
        return collect($cover->get('cover', []));
    }

    /**
     * Сохранение ковера галереи
     * @todo ВАЖНО! добавить проверку прав на галерею.
     * @param string $imageId
     * @return Collection
     */
    public function setCover(string $imageId): Collection
    {
        $image = ImageModel::query()->findOrFail($imageId);
        $image->is_main = 1;
        $image->save();
        $gallery = $this->get();
        $gallery->cover_id = $imageId;
        $gallery->save();
        return collect($image);
    }

    /**
     * @return Collection
     */
    public function getSectionsList(): Collection
    {
        $gallery = $this->get();
        $sections = collect($gallery)
            ->only('sections');

        return collect($sections);
    }

    public function getWebSectionsImages(?string $sectionId = null): Collection
    {
        if (!$this->checkAccess()) {
            throw new ForbiddenException('Доступ к данной галерее возможен только по паролю');
        }

        $result = GallerySectionModel::query()
            ->where('gallery_id', $this->id)
            ->when(!empty($sectionId), function ($q) use ($sectionId) {
                $q->where('id', $sectionId);
            })
            ->with(['images'])
            ->get()
            ->toArray();

        //сортируем изображения
        $result = collect($result);
        return $this->sortSectionsImages($result);
    }

    /**
     * @param $userId
     * @return Collection
     */
    public function getList($userId): Collection
    {
        $limit = config('gallery.galleries.limit');
        $galleries = GalleryModel::query()
            ->where('user_id', $userId)
            ->with(['sections', 'cover'])
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
        //$cover = \App\Models\Image::query()->where(['entity_id' => ])
        return collect($galleries);
    }

    /**
     * @param array $data
     * @return Collection
     * @throws ForbiddenException|\Exception
     */
    public function createSection(array $data): Collection
    {
        if (empty($this->id)) {
            throw new ItemNotFoundException(sprintf('галерея %s не существует', $this->id));
        }

        if (!Permissions::isOwner($this->gallery)) {
            throw new ForbiddenException('галерея принадлежит другому пользователю');
        }

        $sectionLimit = config('utils.max_section_count');
        $sectionCount = GallerySectionModel::query()
            ->where('gallery_id', $this->id)
            ->count();
        if ($sectionCount >= $sectionLimit) {
            throw new \Exception('Достигнут лимит секций', 402);
        }

        $section = new GallerySectionModel($data);
        $section->gallery_id = $this->gallery->id;
        $section->save();
        $section->refresh();

        return collect($section);
    }

    /**
     * @param string $id
     * @param array $data
     * @return Collection
     * @throws ForbiddenException
     */
    public function updateSection(string $id, array $data): Collection
    {
        $data = collect($data);
        $section = GallerySectionModel::query()
            ->where('id', $id)
            ->with('gallery')
            ->first();

        if (!Permissions::isOwner($section->gallery)) {
            throw new ForbiddenException('галерея принадлежит другому пользователю');
        }

        $columns = $section->getFillable();
        $section->fill($data->only($columns)->toArray());

        $section->save();
        $section->refresh();

        return collect($section)
            ->except('gallery');
    }

    /**
     * @param string $id
     * @param string $id
     * @return array
     * @throws ForbiddenException
     */
    public function deleteSection(string $id, string $userId): array
    {
        $section = GallerySectionModel::where('id', $id)
            ->with(['gallery', 'images'])
            ->first();
        if (!Permissions::isOwner($section->gallery)) {
            throw new ForbiddenException('галерея принадлежит другому пользователю');
        }
        $revertId = Str::uuid()->toString();
        $imageRevert = [];
        if (!empty($images = $section->images)) {
            $ids = $images->pluck('id')->toArray();
            $imageRevert = (new Image())->dropImages($ids, $userId);
        }
        Cache::remember(
            'drop_section_uid' . $revertId,
            config('utils.drop_section_cache_time'),
            function () use ($id, $imageRevert) {
                return [
                    'section_id' => $id,
                    'image_revert_id' => Arr::get($imageRevert, 'revert_id')
                ];
            }
        );
        $section->delete();
        return ['revert_id' => $revertId];
    }

    /**
     * @param string $tokenType
     * @param DtoInterface $data
     * @return Collection
     * @throws ItemNotFoundException|ForbiddenException
     */
    public function getData(string $tokenType, DtoInterface $data): Collection
    {
        $userId = null;
        $profile = null;
        $sectionId = $data->get('section_id');

        if ($data->has('profile_id') || $data->has('profile_name')) {
            $profile = new Profile($data->get('profile_id'), $data->get('profile_name'));
            $profile = $profile->get();
            $userId = $profile->user_id;
        }

        $gallery = $this->set($data->get('gallery_id'), $data->get('gallery_name'), $userId);
        $gallery = collect($gallery->get());

        $galleryId = $gallery->get('id');
        if (empty($galleryId)) {
            throw new ItemNotFoundException('галерея не существует');
        }

        $forDownload = ($tokenType == static::ACCESS_TOKEN_TYPES[1]);
        $isSecure = $forDownload
            ? $gallery->get('is_download_secure') : $gallery->get('is_secure');
        if ($isSecure && $gallery->get('user_id') != collect(auth()->user())->get('id')) {
            $token = new GalleryToken($galleryId, $tokenType);
            if (!$token->checkToken($data->get('token'))) {
                throw new ForbiddenException('для доступа к защищенной галерее'
                    . ' требуется запросить и указать валидный токен');
            }
        }

        $images = $this->getImages($sectionId);
        $result = collect($gallery);

        if ($forDownload) {
            $result->put('images', collect());
            $result->put('paths', collect());
        } else {
            $sectionsData = collect();
        }

        if ($images->isEmpty()) {
            throw new NotAcceptable('нет фото для скачивания');
        } else {
            if ($data->isNotEmpty('image_id')) {
                $imageId = $data->get('image_id');
                $images = $images->filter(function ($value, $key) use ($imageId) {
                    return $key == $imageId;
                });
            }

            if ($sectionId) {
                if (!$forDownload) {
                    $section = GallerySectionModel::query()
                        ->find($sectionId);
                    $sectionsData->put($sectionId, collect($section));
                    $sectionsData->get($sectionId)
                        ->put('images', collect());
                }
            }

            $sectionImages = ImageSectionsModel::query()
                ->where('gallery_id', $galleryId)
                ->when($sectionId, function ($query) use ($sectionId) {
                    return $query->where('section_id', $sectionId);
                })
                ->whereIn('image_id', $images->keys()->toArray())
                ->with('section')
                ->get();
            foreach ($sectionImages as $image) {
                $image = collect($image);
                $imageId = $image->get('image_id');
                if ($forDownload) {
                    $result->get('images')
                        ->add(collect($images->get($imageId)->only('images')->values()->get(0)));
                    $result->get('paths')
                        ->add(collect($images->get($imageId)->only('paths')->values()->get(0))
                            ->put('filename', $images->get($imageId)->get('filename')));
                } else {
                    $sectionId = $image->get('section_id');
                    if (!$sectionsData->has($sectionId)) {
                        $sectionsData->put($sectionId, collect($image->get('section')));
                        $sectionsData->get($sectionId)->put('images', collect());
                    }
                    if ($images->get($imageId)) {
                        $sectionsData->get($sectionId)->get('images')
                            ->add($images->get($imageId)->only('images'));
                    }
                }
            }
        }

        //сортировка по секции
        if ($sectionId) {
            if (!$forDownload) {
                $images = $this->sortSection($sectionId, $sectionsData->get($sectionId)->get('images'));
                $sectionsData[$sectionId]->put('images', $images->values());
            } else {
                $images = $this->sortSection($sectionId, $images);
                $result->put('images', $images);
            }
        }

        if (!$forDownload) {
            $result->put('sections', $sectionsData);
        }

        return collect($result)
            ->put('profile', collect($profile)->except('contacts'));
    }

    /**
     * @param DtoInterface $data
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Exception|ForbiddenException
     */
    public function getDownload(DtoInterface $data)
    {
        $imageId = $data->get('image_id');
        $imageType = $data->get('image_type') ?? 'xl';

        $gallery = $this->getData(static::ACCESS_TOKEN_TYPES[1], $data);

        if ($data->isNotEmpty('image_id')) {
            return response()
                ->download($gallery->get('paths')->get(0)->get($imageType));
        } else {
            $galleryId = $gallery->get('id');
            $sectionId = $data->get('section_id');
            $zipArchive = new \ZipArchive();
            $downloadFileName = $sectionId ? $sectionId : $galleryId;
            $downloadFileName = Str::substr(md5($downloadFileName), 0, 6);
            $zipArchiveFolderName = $downloadFileName;
            $downloadFileName .= '.zip';

            $tmpFileUri = sprintf(
                '%s/%s',
                Storage::path('downloads'),
                md5(Carbon::now() . $galleryId . $sectionId . $imageId . $imageType)
            );

            Log::info('file path', [
                $tmpFileUri,
                Storage::path('downloads'),
                Storage::exists(Storage::path('downloads'))
            ]);

            if (!Storage::exists('downloads')) {
                Storage::makeDirectory('downloads', 0775, true);
            }
            if ($zipArchive->open($tmpFileUri, \ZipArchive::CREATE) === true) {
                foreach ($gallery->get('paths') as $path) {
                    $fileName = $path->get($imageType);
                    $userFileName = empty($path->get('filename')) ? basename($fileName) : $path->get('filename');
                    $userFileName = join('/', [$zipArchiveFolderName, $userFileName]);
                    if (!$zipArchive->addFile($fileName, $userFileName)) {
                        throw new \Exception('невозможно добавить изображение в архив', 500);
                    }
                }

                $zipArchive->close();
                return response()
                    ->download($tmpFileUri, $downloadFileName)
                    ->deleteFileAfterSend(true);
            } else {
                throw new \Exception('невозможно создать или открыть архив'
                    . ' для упаковки галереи', 500);
            }
        }
    }

    /**
     * @param string $tokenType
     * @param DtoInterface $data
     * @return Collection
     * @throws ItemNotFoundException|AuthenticationException
     */
    public function getAccess(string $tokenType, DtoInterface $data): Collection
    {
        $userId = null;
        $profile = null;

        if ($data->has('profile_id') || $data->has('profile_name')) {
            $profile = new Profile($data->get('profile_id'), $data->get('profile_name'));
            $profile = $profile->get();
            $userId = $profile->user_id;
        }

        $gallery = $this->set($data->get('gallery_id'), $data->get('gallery_name'), $userId);
        $gallery = collect($gallery->get());
        $galleryId = $gallery->get('id');

        if (empty($galleryId)) {
            throw new ItemNotFoundException('галерея не существует');
        }

        $token = new GalleryToken($galleryId, $tokenType);
        if ($data->get('password') != $token->getGalleryPassword()) {
            throw new AuthenticationException('Вы ввели неверный пароль');
        }

        $result = collect();
        $result->put('access_token', collect($token->getToken())->get('value'));
        $result->put('expires_in', config('gallery.passwords.ttl'));

        return $result;
    }

    /**
     * @param $sectionId
     * @param $userId
     * @return Collection
     */
    public function getSectionImages($sectionId, $userId): Collection
    {
        if (!Permissions::isOwner($this->get(), $userId)) {
            new ForbiddenException('галерея принадлежит другому пользователю');
        }

        $result = collect();
        $result->put('images', collect());

        $images = $this->getImages();

        if ($images->isNotEmpty()) {
            $sectionImages = ImageSectionsModel::query()
                ->where('gallery_id', $this->id)
                ->where('section_id', $sectionId)
                ->whereIn('image_id', $images->keys()->toArray())
                ->get();

            foreach ($sectionImages as $image) {
                $imageId = collect($image)->get('image_id');
                $result->get('images')
                    ->add($images->get($imageId)->get('images'));
            }
        }

        $profile = (new Profile())->setByUserId($userId)->get();

        $result->put('profile', collect($profile)->except('contacts'));

        return $result;
    }

    /**
     * @param null $options
     * @return array
     */
    public function validateGalleryOptions($options = null): array
    {
        $options = is_array($options)
            ? collect($options)
            : collect(json_decode($options, true));
        $userOptions = [];
        $defaultOptions = [];

        collect(config('gallery.default_options'))
            ->each(function ($value, $key) use ($options, &$userOptions, &$defaultOptions) {
                $option = $options->get($key);
                if (!empty($option)) {
                    if (array_key_exists('values', $value) && !in_array($option, $value['values'])) {
                        throw new ItemNotFoundException(sprintf('неизвестное значение %s для опции %s', $option, $key));
                    }

                    $userOptions[$key] = $option;
                }

                $defaultOptions[$key] = $value['default'];
            });

        if (!empty($userOptions)) {
            $userOptions = collect($userOptions)
                ->union($defaultOptions)->toArray();
        } else {
            $userOptions = $defaultOptions;
        }

        return $userOptions;
    }

    /**
     * @param string $id
     * @param string $mode
     * @return Collection
     * @throws ItemNotFoundException|ForbiddenException
     */
    public function updateSectionOrder(string $id, string $mode): Collection
    {
        $section = GallerySectionModel::where('id', $id)
            ->with('gallery')->first();

        if (empty($section)) {
            throw new ItemNotFoundException('секции не существует');
        }

        if (!Permissions::isOwner($section->gallery)) {
            throw new ForbiddenException('галерея принадлежит другому пользователю');
        }

        $section->order_mode = $mode;
        $section->save();
        $section->refresh();

        return collect($section)
            ->except('gallery');
    }

    /**
     * @param string|null $sectionId
     * @return Collection
     */
    private function getImages(?string $sectionId = null): Collection
    {
        $result = (new Image())->getEntityImages(
            $this->id,
            'gallery',
            $sectionId,
            null,
            ['public_paths' => 'images', 'priority' => 'position'],
            [
                'item_id', 'type', 'aspect_ratio', 'crop_bounds', 'crop_paths', 'rotate_angle',
                'public_paths', 'crop_public_paths', 'src', 'src_gallery_mini', 'src_xl', 'priority',
                'creator_id', 'updater_id', 'deleted_at'
            ]
        );

        $result = collect($result);

        if ($result->isNotEmpty()) {
            $result = $result->mapWithKeys(function ($item, $key) {
                return [$item['id'] => collect($item)];
            });
        }

        return collect($result);
    }

    public function getMainGallery(): object
    {
        $galleries = Cache::get('main-galleries');
        if (empty($galleries)) {
            $galleries = Cache::remember(
                'main-galleries',
                180,
                function () {
                    $galleries = GalleryModel::query()
                        ->with(['cover', 'profile'])
                        ->whereIn('id', config('gallery.main_galleries'))
                        ->get();
                    $count = $galleries->count();
                    if ($count < 4) {
                        $galleries = $galleries->merge(GalleryModel::query()
                            ->with(['cover', 'profile'])
                            ->orderBy('created_at', 'desc')
                            ->limit(4 - $count)
                            ->get());
                    }

                    return $galleries;
                }
            );
        }

        return $galleries;
    }

    /**
     * сортировка изображений в секции
     * @param string $sectionId
     * @param Collection $images
     * @return Collection
     */
    public function sortSection(string $sectionId, Collection $images): Collection
    {
        $section = GallerySectionModel::find($sectionId);
        if ($section && $section->order_mode) {
            switch ($section->order_mode) {
                case 'asc':
                {
                    $res = $images->sortBy('filename');
                    break;
                }
                case 'desc':
                {
                    $res = $images->sortByDesc('filename');
                    break;
                }
                case 'manual':
                {
                    $res = $images->sortBy('order');
                    break;
                }
            }
            return $res;
        } else {
            throw new ItemNotFoundException(sprintf('секция %s не существует', $sectionId));
        }
    }

    /**
     * @param string $revertId
     * @return object
     */
    public function revertSection(string $revertId): object
    {
        $data = Cache::get('drop_section_uid' . $revertId);
        if ($data && Arr::has($data, 'section_id')) {
            $id = Arr::get($data, 'section_id');
            $model = GallerySectionModel::withTrashed()->find($id);
            $model->restore();

            if (!empty(Arr::get($data, 'image_revert_id'))) {
                (new Image())->revertImages(Arr::get($data, 'image_revert_id'));
            }

            return $model;
        } else {
            throw new \Exception('Невозможно отменить удаление!');
        }
    }

    public function getSectionPhotos()
    {
        $photos = [];
        if (!empty($this->profile->id)) {
            $photos = (new Image())
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
