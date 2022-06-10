<?php

namespace App\Services\Search;

use App\Models\Building;
use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SearchService
{

    private Building $model;

    public function __construct(Building $buildings)
    {
        $this->model = $buildings;
    }

    /**
     * @param array $bounds
     * @return Collection
     */
    public function getPoints(array $bounds) : Collection{
        // dd($bounds, );
        $point1 = json_decode($bounds[0]);
        $point2 = json_decode($bounds[1]);
        /** @var Polygon $polygon - в яндексе порятдок lng-lat, инвертируем его в lat-lng*/
        $polygon = new Polygon([
            new LineString([
                new Point($point1[1], $point1[0]),
                new Point($point1[1], $point2[0]),
                new Point($point2[1], $point2[0]),
                new Point($point2[1], $point1[0]),
                new Point($point1[1], $point1[0]),
            ])
        ]);
        $objects = $this->model->intersects('coords', $polygon)->get();
        return collect($objects->toBase());
    }

    /**
     * @param string $id
     * @return Collection
     */
    public function getBuildingInfo(string $id) : Collection{
        /** @var Building $object */
        $object = $this->model->with(['type'])->findOrFail($id);
        if ($object->status == 1){
            return collect($object->toBase());
        }else{
            throw new AccessDeniedHttpException('Object is not accessible for you, my darling');
        }
    }

}
