<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Resources\DistrictResource;
use App\Models\City;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::query()
            ->orderBy('id')
            ->get();

        return CityResource::collection($cities);
    }

    public function districts(int $cityId)
    {
        $city = City::query()->findOrFail($cityId);

        $districts = $city->districts()
            ->orderBy('postal_code')
            ->orderBy('id')
            ->get();

        return DistrictResource::collection($districts);
    }
}
