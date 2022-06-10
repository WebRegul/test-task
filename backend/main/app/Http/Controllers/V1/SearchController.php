<?php

namespace App\Http\Controllers\V1;

use App\Services\Search\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * @var SearchService
     */
    protected SearchService $search;

    public function __construct(SearchService $search)
    {
        $this->search = $search;
    }

    public function getList(): JsonResponse
    {
        return response()->json(['xxx'=>'test']);
    }

    public function getMap(Request $request): JsonResponse
    {
        $res = $this->search->getPoints($request->get('bounds'));
        return response()->json($res);
    }

    public function getInfo(string $id, Request $request): JsonResponse
    {
        $res = $this->search->getBuildingInfo($id);
        return response()->json($res);
    }
}
