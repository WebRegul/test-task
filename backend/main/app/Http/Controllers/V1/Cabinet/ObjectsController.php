<?php

namespace App\Http\Controllers\V1\Cabinet;

use App\Exceptions\ForbiddenException;
use App\Facades\Member;
use App\Http\Requests\Cabinet\ChangePasswordRequest;
use App\Http\Requests\Cabinet\ChangePasswordVerifyRequest;
use App\Http\Requests\Cabinet\ChangePhoneRequest;
use App\Http\Requests\Cabinet\ChangePhoneVerifyRequest;
use App\Http\Requests\Cabinet\ProfileUpdateRequest;
use App\Jobs\DeleteProfileJob;
use App\Models\Building;
use App\Services\Profile;
use App\Services\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ObjectsController extends BaseController
{
    public function getList(Request $request){
        $limit = $request->get('limit', 10);
        $status = $request->get('status');
        $query = Building::query();
        if(!is_null($status)){
            $query->where('status', $status);
        }

        $data = $query->paginate($limit)->through(function ($e) {
            $e['price'] = [
                'type'     => 'fixed',
                'value'    => 1500 * $e['id'],
                'currency' => 'RUB'
            ];
            $e['rating'] = [
                'value'  => 3.5,
                'detail' => [],
            ];
            $e['reviews'] = [
                'count' => rand(0, 1000),
                'list'  => [],
            ];
            $e['address'] = 'Россия,г. Сочи, ул. Депутатская ' . $e['id'];
            return $e;
        });

        return response()->json($data);
    }

    public function getCounts(){
        $data = Building::query()->groupBy('status')->select(['status', DB::raw('count(*) as total')])
            ->get()->collect();
        return response()->json($data);
    }

    public function getDetail(Request $request, int $id){
        $data = Building::query()->find($id);
        $data['prices'] = [
            [
                'type'     => 'fixed',
                'value'    => 1500 * $id,
                'currency' => 'RUB',
                'max_guests' => 4,
                'nutrition' => 'none'
            ],
            [
                'type'     => 'fixed',
                'value'    => 2000 * $id,
                'currency' => 'RUB',
                'max_guests' => 8,
                'nutrition' => 'none'
            ]
        ];
        $data['address'] = 'Россия,г. Сочи, ул. Депутатская ' . $id;
        return $data;
    }
}
