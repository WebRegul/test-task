<?php

namespace App\Helpers\Dto;

interface DtoInterface
{
    /**
     * set key in dto object
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set(string $key, $value);

    /**
     * mass set keys in dto object from array
     * @param array $data
     * @return mixed
     */
    public function setFromArray(array $data);

    /**
     * get value by key from dto object
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * remove key from dto object
     * @param $key
     * @return mixed
     */
    public function remove(string $key);

    /**
     * check key exists in dto object
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * check key value is not empty
     * @param string $key
     * @return bool
     */
    public function isNotEmpty(string $key): bool;

    /**
     * check key value is empty
     * @param string $key
     * @return bool
     */
    public function isEmpty(string $key): bool;

    /**
     * get all data from dto object
     * @return array
     */
    public function all(): array;
}
