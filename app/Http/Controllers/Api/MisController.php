<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CountryResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\CityResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

class MisController  extends BaseController
{
    //
    public function getAllCountry(){
        return $this->sendResponse(CountryResource::collection(Country::all()), 'Country fetched successfully.');

    }

    public function getStateByCountry($country_id){

        $getStateById = State::whereCountryId($country_id)->get();
        return $this->sendResponse(StateResource::collection($getStateById), 'State fetched successfully.');

    }
    public function getCityByState($state_id){

        $getCityById = City::whereStateId($state_id)->get();
        return $this->sendResponse(CityResource::collection($getCityById), 'City fetched successfully.');

    }

}
