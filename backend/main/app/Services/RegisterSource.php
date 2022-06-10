<?php

namespace App\Services;

use App\Models\RegisterSource as RegisterSourceModel;
use Illuminate\Support\Collection;
use Illuminate\Support\ItemNotFoundException;

/**
 * Class RegisterSource
 * @package App\Services
 */
class RegisterSource
{
    /**
     * @param string $name
     * @return string
     */
    public function getIdByName(string $name): string
    {
        $registerSource = RegisterSourceModel::query()
            ->where('name', $name)
            ->first();

        if (empty($registerSource)) {
            throw new ItemNotFoundException(
                sprintf('источник регистрации %s не существует', $name)
            );
        }

        return $registerSource->id;
    }

    /**
     * @param string $id
     * @return Collection
     */
    public function getById(string $id): Collection
    {
        $registerSource = RegisterSourceModel::query()
            ->find($id);

        if (empty($registerSource)) {
            throw new ItemNotFoundException(
                sprintf('источник регистрации %s не существует', $id)
            );
        }

        return collect($registerSource);
    }
}
