<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Member extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Registries\Member::class;
    }

    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();

        if (! $instance) {
            throw new \RuntimeException('A facade root has not been set.');
        }

        return $instance->$method(...$args);
    }
}
