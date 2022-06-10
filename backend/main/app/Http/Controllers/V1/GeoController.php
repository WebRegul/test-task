<?php

namespace App\Http\Controllers\V1;

use App\Models\City;
use App\Services\Geo\CitiesService;
use Illuminate\Http\JsonResponse;

class GeoController extends Controller
{
    /**
     * @var CitiesService
     */
    protected CitiesService $cities;

    public function __construct(CitiesService $cities)
    {
        $this->cities = $cities;
    }

    /**
     * @return JsonResponse
     */
    public function getCities(): JsonResponse
    {
        return response()->json($this->cities->getList()->toArray());
    }
}
