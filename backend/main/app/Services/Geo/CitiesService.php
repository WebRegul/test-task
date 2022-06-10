<?php
namespace App\Services\Geo;

use App\Models\City;
use Illuminate\Support\Collection;

class CitiesService
{
    /**
     * @var City
     */
    protected City $city;

    public function __construct(City $city)
    {
        $this->city = $city;
    }

    /**
     * @return Collection
     */
    public function getList(): Collection
    {
        return $this->city::query()->with(['country'])->get()->toBase();
    }
}
