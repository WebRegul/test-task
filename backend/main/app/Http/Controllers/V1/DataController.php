<?php

namespace App\Http\Controllers\V1;

use App\Models\BuildingType;
use App\Services\Search\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DataController extends Controller
{
    /**
     * @var SearchService
     */
    protected SearchService $search;

    public function __construct(SearchService $search)
    {
        $this->search = $search;
    }

    public function getParams(): JsonResponse
    {
        $result = collect([]);
        $buildingTypes = BuildingType::query()
            ->select(['id', 'name', 'title', 'icon'])
            ->where(['status' => 1])->orderBy('order')->get();
        $result->put('building_types', $buildingTypes);
        return response()->json($result);
    }

}
