<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CountryResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\CityResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\JobCategory;
use App\Models\State;
use App\Models\User;
use App\Models\City;

class MisController  extends BaseController
{
    //
    public function getAllCountry(){
        return $this->sendResponse(CountryResource::collection(Country::all()), 'Country fetched successfully.');

    }

    public function getStateByCountry($country_id){

        $getStateById = State::whereCountryId($country_id)->orderBy('name')->get();
        return $this->sendResponse(StateResource::collection($getStateById), 'State fetched successfully.');

    }
    public function getCityByState($state_id){

        $getCityById = City::whereStateId($state_id)->orderBy('name')->get();
        return $this->sendResponse(CityResource::collection($getCityById), 'City fetched successfully.');

    }

    public function getAllJobCategory(){
        $Jobcategories = JobCategory::with('countDesig')->get();
        if(!$Jobcategories){
            return $this->sendError('Error fetching the Job categories', []);
        }
        return $Jobcategories;
    }

    public function getUserByDesignation($designation){
        $findUSerWithDesignation = User::where('designation',$designation)->get();
        if(!$findUSerWithDesignation){
            return $this->sendError('Error fetching User by designation', []);
        }
        return $this->sendResponse($findUSerWithDesignation, 'Users Fetched successfully.');
    }

}
