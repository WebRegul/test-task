<?php

namespace App\Helpers\Dto;

/**
 * класс по шаблону проектирования DTO
 * определение ключа происходит единожды
 *
 * Class OnceDto
 * @package App\Helpers\Dto
 */
class OnceDto implements DtoInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * определение ключа происходит единожды
     *
     * @param string $key
     * @param $value
     * @return void
     * @throws \Exception
     */
    public function set(string $key, $value)
    {
        if (!$this->has($key)) {
            $this->data[$key] = $value;
        } else {
            throw new \Exception(sprintf('попытка переопределения ключа %s', $key));
        }
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function setFromArray(array $data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        return $this->has($key) ? $this->data[$key] : $default;
    }

    /**
     * @param string $key
     * @return void
     */
    public function remove(string $key)
    {
        if ($this->has($key)) {
            unset($this->data[$key]);
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isNotEmpty(string $key): bool
    {
        return !$this->isEmpty($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isEmpty(string $key): bool
    {
        return empty($this->get($key));
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }
}
