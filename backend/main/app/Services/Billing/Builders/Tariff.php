<?php

namespace App\Services\Billing\Builders;

use App\Exceptions\PreconditionFailedException;
use App\Registries\Member;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use App\Models\Tariff as TariffModel;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;
use function Symfony\Component\String\b;

/**
 * @Annotation
 * Класс строитель для работы с моделью тарифа,
 * Работает всегда с тарифом в контексте одного конкретного, с текущим и новым
 */
/**
 * Class Tariff
 * @package App\Services\Billing\Builders
 *
 * все свойства ниже относятся к обрабатываемому тарифу
 * @property-read string $id
 * @property integer $size
 * @property float $price_month
 * @property float $price_year
 * @property float $amount - вычисленное свойство, стоимость тарифа для юзера с учетом периода оплаты и скидок
 * @property string $period - вычисленное свойство, период на который покупается подписка
 * @property integer $discount_year
 * @property string $name
 * @property string $description
 */
class Tariff
{
    /**
     * Возможные ошибки при создании или смене тарифа и их копирайты
     */
    public const ERRORS = [
        'LARGE_TARIFF' => 'У вас уже активирован более дорогой тариф',
        'LARGE_PERIOD' => 'У вас уже активирована годовая подписка',
        'DUPLICATE' => 'У вас уже активирован этот тариф',
    ];

    public const PERIOD_MONTH = 'month';
    public const PERIOD_YEAR = 'year';

    /**
     * Префикс ключа для хранения тарифа в кеше
     * @const string
     */
    public const TARIFF_CACHE_PREFIX = 'tariffs.id-';

    /**
     * Идентификатор тарифа
     * @var string
     */
    private string $id;

    /**
     * Период тарифа
     * @var string
     */
    protected string $period;

    /**
     * Стоимость тарифа с учетом периода оплаты
     * @var float
     */
    protected float $amount;

    /**
     * @var TariffModel|mixed
     */
    protected TariffModel $tariff;

    /**
     * @var Member
     */
    protected Member $member;

    /**
     * Профиль пользователя с которым работаем
     * @var Collection
     */
    protected Collection $profile;

    /**
     * Коллекция с данными тарифа
     * @var Collection
     */
    protected Collection $data;

    /**
     * Коллекция с данными текущего тарифа юзера
     * @var Collection
     */
    protected Collection $current;

    /**
     * @param string $id - id тарифа, который хотим установить юзеру
     * @param string $period - период оплаты этого тарифа
     * @param Member|null $member - пользователь
     * @throws UserNotDefinedException
     * @throws ModelNotFoundException
     */
    public function __construct(string $id, string $period, ?Member $member = null)
    {
        $this->id = $id;
        $this->period = $period;


        $this->setMemberData($member);

        $this->setTariff();

        $this->setPrices();
    }

    /**
     * @return Collection
     */
    public function get(): Collection
    {
        return $this->data;
    }

    /**
     * Стоимость тарифа с учетом периода
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @throws PreconditionFailedException
     */
    public function getFinishDate(): Carbon
    {
        $date = Carbon::now();
        switch ($this->period) {
            case 'month':
                return $date->addMonth();
                break;
            case 'year':
                return $date->addYear();
                break;
            default:
                throw new PreconditionFailedException('Недопустимый период тарифа');
        }
    }

    /**
     * Верифицирует процесс и возможность перехода с текущего тарифа на новый
     * @throws PreconditionFailedException
     */
    public function verify()
    {
        $priceOld = $this->price($this->current, $this->period);

        if ($this->data->get('id') == $this->current->get('id') && $this->period == $this->current->get('period')) {
            throw new PreconditionFailedException(static::ERRORS['DUPLICATE']);
        }
        if ($priceOld > $this->amount) {
            throw new PreconditionFailedException(static::ERRORS['LARGE_TARIFF']);
        }
        if ($this->period == self::PERIOD_MONTH && $this->profile->get('tariff_period') == self::PERIOD_YEAR) {
            throw new PreconditionFailedException(static::ERRORS['LARGE_PERIOD']);
        }
    }

    public function validate(string $id, string $period)
    {
        //if(!$this->)
    }

    /**
     * @return bool
     */
    public function isFree(): bool
    {
        return ($this->tariff->price_month == 0 && $this->tariff->price_year == 0);
    }

    /**
     * @param string $period
     */
    public function setPeriod(string $period): void
    {
        $this->period = $period;
    }

    /**
     * @param Collection $tariff
     * @param string $period
     * @return mixed
     */
    protected function price(Collection $tariff, string $period)
    {
        return static::realPrice($tariff, $period);
    }

    /**
     * @param Collection $tariff
     * @param string $period
     * @return float
     */
    public static function realPrice(Collection $tariff, string $period): float
    {
        $price = $tariff->get('price_' . $period);
        $m = ($period == 'year') ? 12 : 1;

        return $price * $m;
    }

    /**
     * Перехват обращений к закрытым или несуществующим свойствам,
     * Возвращает ВСЕГДА данные тарифа с которым работаем, т.е. нового
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->data->get($name);
    }

    /**
     * Вычисляет и устанавливает реальную стоимость с учетом периода для текущего и нового тарифного плана
     */
    protected function setPrices()
    {
        $this->amount = $this->price($this->data, $this->period);
        $this->data->put('amount', $this->amount);
        $this->current->put('amount', $this->price($this->current, $this->profile->get('tariff_period')  || static::PERIOD_MONTH));
    }

    /**
     * Получает и устанавливает данные пользователя и его текущего тарифа
     * @param Member|null $member
     * @return void
     * @throws UserNotDefinedException
     */
    protected function setMemberData(?Member $member)
    {
        $this->member = $member ?? app(Member::class);
        if (!$this->member || !$this->member->id) {
            throw new UserNotDefinedException('Пользователь не найден. Работа с тарифом возможна только с реальным пользователем', 401);
        }
        $this->profile = collect($this->member->profile);
        $this->current = collect($this->member->tariff);
    }

    /**
     * Получает и устанавливает данные тарифа для работы
     * @return void
     * @throws ModelNotFoundException
     */
    protected function setTariff()
    {
        $this->tariff = new TariffModel();
        try {
            $this->tariff = Cache::rememberForever($this->getCacheKey(), function () {
                return $this->tariff->findOrFail($this->id);
            });
        } catch (\Exception $exception) {
            throw new ModelNotFoundException('Tariff not found', 404, $exception);
        }
        $this->data = collect($this->tariff);
        $this->data->put('period', $this->period);
    }

    /**
     * @return string
     */
    protected function getCacheKey(): string
    {
        return static::TARIFF_CACHE_PREFIX . $this->id;
    }
}
