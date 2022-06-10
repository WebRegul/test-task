<?php

namespace App\Registries;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Базовый класс рееализации паттерна Registry
 * Class BaseRegistry
 * @package Common\Registries
 */
abstract class BaseRegistry
{
    /**
     * @var array
     */
    protected array $registry = [];

    /**
     * добавление значения в реестр.
     * так же, если в дочернем классе определено свойстство с именем этого ключа - значение будет установлено и в него
     * @param string $key
     * @param $value
     */
    final public function set(string $key, $value): void
    {
        Arr::set($this->registry, $key, $value);
        if (property_exists($this, $key)) {
            $this->$key = $value;
        }
    }

    /**
     * Получение одного значения из реестра.
     * Допустима dot нотация (key1.key2) для получения вложенных значений
     * @param string $key
     * @return mixed
     */
    final public function get(string $key)
    {
        return Arr::get($this->registry, $key);
    }

    /**
     * Добавление значений в реестр в виде массива key => value
     * Значения добавляются с переданными ключами
     * @param array $data
     */
    final public function setArray(array $data = []): void
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Очистка всех значений реестра
     */
    final public function flush(): void
    {
        $this->registry = [];
    }

    /**
     * Получение всех значений реестра в виде коллекии
     * @return Collection
     */
    final public function all(): Collection
    {
        return collect($this->registry);
    }


    /**
     * перехват обращения к несуществующим свойствам объекта и возвращение для них значения из реестра
     * для обращения к ним в виде $registry->[key]
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }
}
