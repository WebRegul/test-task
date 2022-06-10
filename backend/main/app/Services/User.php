<?php

namespace App\Services;

use App\Events\UpdateMemberEvent;
use App\Exceptions\IsVerifiedException;
use App\Exceptions\PreconditionFailedException;
use App\Helpers\Network;
use App\Helpers\Permissions;
use App\Models\PasswordReset;
use App\Models\PreregistrationUser as PreregistrationUserModel;
use App\Registries\Member as MemberRegistry;
use App\Services\Images\Image;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User as UserModel;
use App\Models\Profile as ProfileModel;
use App\Models\Tariff as TariffModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\Str;
use App\Services\Billing\Builders\Tariff as TariffBuilder;

/**
 * Class User
 * @package App\Services
 */
class User
{
    /** @var string префикс для кеширования нового телефона  юзера при смене */
    public const CACHE_USER_NEW_PHONE_PREFIX = 'USER.PHONE.NEW:';

    /**
     * @var string
     */
    private ?string $id = null;

    /**
     * @var UserModel|Authenticatable|null
     */
    private $user;

    /**
     * @var ProfileModel
     */
    private ProfileModel $profile;

    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    public const STORAGE_SIZE_UNIT = 'M';

    /**
     * @var string
     */
    public const MEMBER_CACHE_PREFIX = 'MEMBER.USER:';

    /**
     * @var string
     */
    public const TARIFF_CACHE_PREFIX = 'TARIFF:';

    /**
     * User constructor.
     * @param string|null $id
     * @throws \Exception
     */
    public function __construct(?string $id = null)
    {
        $this->set($id);
        $this->setMemberData();
    }

    /**
     * @param MemberRegistry $registry
     */
    public function setMember(MemberRegistry $registry): void
    {
        $registry->setArray($this->getMemberData()->toArray());
    }

    private function set(?string $id = null): void
    {
        $this->id = $id ?? auth()->id();

        $this->user = UserModel::query()
            ->find($this->id);

        $profile = new Profile();
        $profile->setByUserId($this->id, true);
        $this->profile = $profile->get();
    }

    /**
     * @return Authenticatable|UserModel|null
     */
    private function get()
    {
        return $this->user;
    }

    /**
     * @param string $login
     * @param string $password
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $registerSource
     * @return Collection
     */
    public function registration(
        string  $login,
        string  $password,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $registerSource = null
    ): Collection {
        return DB::transaction(function () use ($login, $password, $firstName, $lastName, $registerSource) {
            $user = UserModel::query()
                ->where('login', $login)
                ->firstOrNew();
            $isNewUser = empty($user->id);

            if (Permissions::isVerified($user)) {
                throw new IsVerifiedException('пользователь уже верифицирован');
            }

            $user->login = $login;
            $user->password = Hash::make($password);
            // $user->password_updated_at = Carbon::now()->toDateTimeString();
            $user->save();
            $user->refresh();

            (new Profile())->create($user->id, [
                'name' => $firstName,
                'surname' => $lastName,
                'source_name' => $registerSource,
            ]);

            $instance = new static($user->id);
            $instance->setMember(app(MemberRegistry::class));

            $result = collect();
            $result->put('user_id', $user->id);

            // (new UserStorage($user->id))->create();

            //код вызывается отдельным запросом с фронта
            //            if ($isNewUser) {
            //                $userCode = app(UserCode::class)->sendCode();
            //                if (config('sms_code.debug')) {
            //                    $result->put('code', $userCode);
            //                }
            //
            //                app(UserCode::class)->clearCodes();
            //            }

            return $result;
        });
    }

    /**
     * @param array $contacts
     * @return Collection
     */
    public function createRegistrationContacts(array $contacts): Collection
    {
        $contacts = DB::transaction(function () use ($contacts) {
            $result = collect();

            foreach ($contacts as $key => $value) {
                $result->add(app(UserContact::class)->create($key, $value));
            }

            return $result;
        });

        return collect($contacts);
    }

    /**
     * @return JsonResponse
     * @throws IsVerifiedException
     */
    public function repeatSendCode(): JsonResponse
    {
        $user = $this->get();

        if (Permissions::isVerified($user)) {
            throw new IsVerifiedException('пользователь уже верифицирован');
        }

        $this->setMember(app(MemberRegistry::class));

        $userCode = app(UserCode::class)->repeatSendCode();

        $result = collect();
        $result->put('user_id', $this->id);

        if (config('sms_code.debug')) {
            $result->put('code', $userCode);
        }
        //!!! не надо возвращать респонсы из сервисов! только данные. Рабата с респонсом компетенция контроллера.
        // сервис должен быть универсален и заниматься логикой, которая может быть прокинута куда угодно,
        // хоть в контроллер, хоть в консоль, хоть в очередь, хоть в событие, хоть в разные шлюзы (где один может джесон выкидывать, а другой хмл)
        return response()->json($result);
    }

    public function sendSmsCode($data)
    {
        $sUid = Arr::get($data, 'uid');
        if (Cache::get($sUid)) {
            $user = Cache::get($sUid);
            $this->set($user->id);
            $this->setMemberData();

            $this->setMember(app(MemberRegistry::class));

            $userCode = app(UserCode::class)->sendCode();

            $result = collect();
            $result->put('user_id', $this->id);

            if (config('sms_code.debug')) {
                $result->put('code', $userCode);
            }
            return response()->json($result);
        } else {
            return response(['status' => false, "message" => "Отправка смс кода невозможна. Некорректное значение параметра uid"], 400);
        }
    }

    public function checkSmsCode($data)
    {
        $sUid = Arr::get($data, 'uid');
        $resetId = Arr::get($data, 'reset_id');
        $code = Arr::get($data, 'code');
        if (Cache::get($sUid)) {
            return DB::transaction(function () use ($sUid, $resetId, $code) {
                $resetPassword = PasswordReset::where('id', $resetId)->where('status', 0)->first();
                if (!$resetPassword) {
                    throw new AuthenticationException('ошибка при подтверждении кода');
                }
                $user = Cache::get($sUid);
                $this->set($user->id);
                $this->setMemberData();
                $this->setMember(app(MemberRegistry::class));
                if (!app(UserCode::class)->verifyCode($code)) {
                    throw new AuthenticationException('неверный код подтверждения');
                }

                $resetPassword->status = 1;
                $resetPassword->save();

                return ['status' => true, "message" => "Код подтвержден!"];
            });
        } else {
            return response(['status' => false, "message" => "Подтверждение смс кода невозможно"], 400);
        }
    }

    /**
     * @param string|null $code
     * @param bool $autoVerify
     * @return string
     * @throws AuthenticationException
     * @throws IsVerifiedException
     */
    public function verify(?string $code = null, bool $autoVerify = false): string
    {
        $user = $this->get();

        if (Permissions::isVerified($user)) {
            throw new IsVerifiedException('пользователь уже верифицирован');
        }

        $this->setMember(app(MemberRegistry::class));

        if (!$autoVerify) {
            if (!app(UserCode::class)->verifyCode($code)) {
                throw new AuthenticationException('неверный код подтверждения');
            }
        }

        $user = DB::transaction(function () use ($user, $code, $autoVerify) {
            $user->verified_at = Carbon::now()->toDateTimeString();
            $user->save();
            $user->refresh();

            $instance = new static($user->id);
            $instance->setMember(app(MemberRegistry::class));

            if (!$autoVerify) {
                $userCode = app(UserCode::class);
                $userCode->makeCodeSigned($code);
                $userCode->clearCodes();
            }

            return $instance;
        });

        $token = auth()->login($user->get());

        return $token;
    }

    /**
     * Собирает и устанавливает данные по текущему пользователю
     * @throws \Exception
     */
    protected function setMemberData(): void
    {
        $this->data = Cache::rememberForever($this->getMemberCacheKey(), function () {
            $this->data['guest'] = (!$this->id);
            $this->data['id'] = $this->id;
            $this->data['user'] = collect($this->user)->toArray(); //зачем убивать коллекцию и делать массив, если мы потом все равно везде обратное делаем?
            $this->data['profile'] = $this->profile
                ? collect($this->profile)->toArray() //зачем убивать коллекцию и делать массив, если мы потом все равно везде обратное делаем?
                : collect([]);
            //$this->data['register_source'] = $this->getRegisterSource();
            //$this->data['tariff'] = $this->getTariff();
            if (isset($_SERVER['SERVER_PROTOCOL'])) {
                $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === 0 ? 'https://' : 'http://';
                $this->data['domain'] = $protocol . $_SERVER['HTTP_HOST'];
            }

            $photos = [];
//            if (!empty($this->profile->id)) {
//                $photos = (new Image())
//                    ->getEntityImages(
//                        $this->profile->id,
//                        'profile',
//                        null,
//                        null,
//                        [
//                            'crop_public_paths' => 'photos'
//                        ],
//                        [
//                            'src_xl', 'src_gallery_mini', 'paths', 'public_paths',
//                            'crop_paths', 'crop_public_paths', 'crop_bounds', 'rotate_angle',
//                            'priority', 'transformations'
//                        ]
//                    );
//                $photos = collect($photos)->get(0);
//            }

            $this->data['photo'] = $photos ? collect($photos)->get('photos') : collect([]);

//            if (!empty($this->id)) {
//                $this->data['storage'] = [
//                    'occupied_space' => $this->getStorageSize(),
//                    'available_space' => $this->getAvailableStorageSize(),
//                    'all_space' => $this->getUserTariffSize()
//                ];
//            }

            return $this->data;
        });
    }

    /**
     * @param string|null $key
     * @return Collection|mixed
     */
    public function getMemberData(?string $key = null)
    {
        $data = collect($this->data);

        return !empty($key) ? $data->get($key) : $data;
    }

    /**
     * @return void
     */
    public function resetMemberData(): void
    {
        Cache::forget($this->getMemberCacheKey());
        $this->setMemberData();
    }

    private function getMemberCacheKey()
    {
        return static::MEMBER_CACHE_PREFIX . $this->id;
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    private function getRegisterSource(): Collection
    {
        return collect([]);
//        return !empty($this->profile->id)
//            ? app(RegisterSource::class)->getById($this->profile->register_source_id)
//            : collect([]);
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    private function getTariff(): Collection
    {
//        if (!empty($this->profile) && $this->profile->tariff_id) {
//            $tariff = Cache::rememberForever(static::TARIFF_CACHE_PREFIX . $this->profile->tariff_id, function () {
//                /** @var TariffModel $model */
//                $model = TariffModel::find($this->profile->tariff_id);
//                return collect($model);
//            });
//
//            $tariff->put('renuval_date', $this->profile->tariff_finished_at);
//            $tariff->put('renuval_amount', TariffBuilder::realPrice($tariff, $this->profile->tariff_period ?: TariffBuilder::PERIOD_MONTH));
//            $tariff->put('renuval_period', $this->profile->tariff_period);
//            return $tariff;
//        }

        return collect([]);
    }


    /**
     * @return float
     * @throws \Exception
     */
    public function getStorageSize(): float
    {
        return 0;
        //$userStorage = new UserStorage($this->id);

        //return $userStorage->getStorageSize(static::STORAGE_SIZE_UNIT);
    }

    /**
     * @return float
     * @throws \Exception
     */
    public function getAvailableStorageSize(): float
    {
        return 0;
//        if (!$this->profile->tariff_id) {
//            throw new \Exception('У вас не выбран тариф!');
//        }
//
//        $storageSize = $this->getStorageSize();
//        $userTariffSize = $this->getUserTariffSize();
//        return $userTariffSize - $storageSize;
    }

    /**
     * @return Collection
     */
    public function getUserTariff(): Collection
    {
        return collect([]);
//        $storageAvailableSize = $this->getAvailableStorageSize();
//
//        $userTariff = TariffModel::find($this->profile->tariff_id);
//        if ($userTariff) {
//            $userTariff = collect($userTariff);
//            $userTariff->put('storage', [$storageAvailableSize, static::STORAGE_SIZE_UNIT]);
//        } else {
//            throw new \Exception('Неизвестный тариф!');
//        }
//
//        return $userTariff;
    }

    public function getUserTariffSize()
    {
        return 0;
//        $userTariff = TariffModel::find($this->profile->tariff_id);
//        return Arr::get($userTariff, 'size') * 1024;
    }

    /**
     * @param array $data
     * @return Collection
     */
    public function preregistration(array $data): Collection
    {
        $data = collect($data);
        $user = PreregistrationUserModel::query()
                ->where('phone', $data->get('phone'))
                ->first()
            ?? new PreregistrationUserModel($data->toArray());
        $user->ip = Network::getIp();

        $user->save();
        $user->refresh();

        return collect($user);
    }

    /**
     * @param string $phone
     * @return array
     */
    public function resetPassword(string $phone): array
    {
        return DB::transaction(function () use ($phone) {
            $user = UserModel::where('login', $phone)->first();
            if (!$user) {
                throw new ItemNotFoundException("Восстановление пароля невозможно. Пользователя с таким номером телефона не существует");
            }
            $oReset = new PasswordReset(['user_id' => $user->id, 'created_at' => Carbon::now()]);
            $oReset->save();
            $sUid = Str::uuid()->toString();
            Cache::forever($sUid, $user);
            return ['status' => true, "uid" => $sUid, 'reset_id' => $oReset->id];
        });
    }

    /**
     * @param array $data
     * @return array
     */
    public function updatePassword(array $data)
    {
        return DB::transaction(function () use ($data) {
            $resetPassword = PasswordReset::find(Arr::get($data, 'reset_id'));
            if (!$resetPassword) {
                throw new ItemNotFoundException('Ошибка!. reset_id не найден!');
            }
            $date = Carbon::now();
            if ($date->diffInMinutes($resetPassword->created_at) > 10) {
                $resetPassword->status = 3;
                $resetPassword->save();
                throw new Exception('Ошибка!. Сессия приостановлена');
            }
            if ($resetPassword->status == 1) {
                $user = UserModel::find($resetPassword->user_id);
                $user->password = Hash::make(Arr::get($data, 'password'));
                $user->password_updated_at = Carbon::now()->toDateTimeString();
                $user->save();
                $resetPassword->status = 2;
                $resetPassword->save();
                $token = auth()->login($user);
                return ['status' => true, "token" => $token];
            } else {
                throw new Exception('ошибка при изменении пароля');
            }
        });
    }

    /**
     * Изменение телефона пользователя. Шаг 1
     * @param string $phone
     * @return Collection
     */
    public function changePhone(string $phone): Collection
    {
        /** @var MemberRegistry $member */
        $member = app(MemberRegistry::class);

        Cache::put(static::CACHE_USER_NEW_PHONE_PREFIX . $member->id, $phone, config('utils.new_phone_cache_time'));
        /** обновляем в мембере логин без сохранения в бд для отправки на новый номер сообщения */
        $member->set('user', array_merge($member->get('user'), ['login' => $phone]));
        /** @var string $userCode - смс код ушедший на новый номер */
        $userCode = app(UserCode::class)->sendCode();
        $result = collect();
        if (config('sms_code.debug')) {
            $result->put('code', $userCode);
        }
        $result->put('message', 'На новый номер телефона отправлен код');
        return $result;
    }

    /**
     * Получение и проверка кода подтверждения с нового номера телефона при смене
     * @param string $code
     * @return Collection
     */
    public function changePhoneVerify(string $code): Collection
    {
        /** @var MemberRegistry $member */
        $member = app(MemberRegistry::class);

        if (!app(UserCode::class)->verifyCode($code)) {
            throw new AuthenticationException('Неверный код подтверждения');
        }

        /** @var string $phone - номер телефона из кеша записанный на первом шаге смены */
        $phone = Cache::pull(static::CACHE_USER_NEW_PHONE_PREFIX . $member->id);

        if (!$phone) {
            throw new PreconditionFailedException('Время на изменение номера истекло');
        }

        $this->user->login = $phone;
        $this->user->save();
        $member->set('user', $this->user->toArray());

        return collect(['member' => $member->all(), 'message' => 'Номер телефона изменен']);
    }

    /**
     * @param string $oldPassword
     * @return Collection
     */
    public function changePassword(string $oldPassword): Collection
    {
        if (!Hash::check($oldPassword, $this->user->password)) {
            throw new IsVerifiedException('Неверно указан пароль');
        }
        $res = $this->resetPassword($this->user->login);
        return collect(['reset_id' => Arr::get($res, 'reset_id')]);
    }

    /**
     * @param array $data
     * @return Collection
     */
    public function changePasswordVerify(array $data): Collection
    {
        return DB::transaction(function () use ($data) {
            $resetPassword = PasswordReset::find(Arr::get($data, 'reset_id'));
            if (empty($resetPassword)) {
                throw new ItemNotFoundException('Ошибка!. reset_id не найден');
            }
            $date = Carbon::now();
            if ($date->diffInMinutes($resetPassword->created_at) > config('utils.reset_password_cache_time')) {
                $resetPassword->status = 3;
                $resetPassword->save();
                throw new AuthenticationException('Ошибка!. Сессия приостановлена');
            }
            if ($resetPassword->status == 0) {
                $this->user->password = Hash::make(Arr::get($data, 'password'));
                $this->user->password_updated_at = Carbon::now()->toDateTimeString();
                $this->user->save();
                $resetPassword->status = 2;
                $resetPassword->save();
                event(new UpdateMemberEvent($this->user->id));
                return collect(["message" => 'Новый пароль установлен!']);
            } else {
                throw new Exception('ошибка при изменении пароля');
            }
        });
    }

    /**
     * @param string $uid
     * @return string
     */
    public function loginByUid(string $uid): string
    {
        $token = '';
        if ($authUser = Cache::get($uid)) {
            Cache::forget($uid);

            $user = UserModel::query()
                ->where('login', Arr::get($authUser, 'login'))
                ->first();
            $token = auth()->login($user);
        } else {
            throw new Exception('неизвестный uid', 400);
        }

        return $token;
    }
}
