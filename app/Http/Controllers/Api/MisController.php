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
use App\Models\SpecialtyList;
use App\Models\OfferType;
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
        $Jobcategories = JobCategory::withCount('countDesig')->get();
        if(!$Jobcategories){
            return $this->sendError('Error fetching the Job categories', []);
        }
        return $this->sendResponse($Jobcategories, 'Job Categories Fetched successfully.');
    }

    public function getUserByDesignation($designation){
        $findUSerWithDesignation = User::where('designation',$designation)->get();
        if(!$findUSerWithDesignation){
            return $this->sendError('Error fetching User by designation', []);
        }
        return $this->sendResponse($findUSerWithDesignation, 'Users Fetched successfully.');
    }

    public function getAllOfferType(){
        $offerTypes = OfferType::all();
        if(!$offerTypes){
            return $this->sendError('Error fetching offer types', []);
        }
        return $this->sendResponse($offerTypes, 'Offer types Fetched successfully.');

    }

    public function getAllSpecialtyList(){
        $specialtyLists = SpecialtyList::all();
        if(!$specialtyLists){
            return $this->sendError('Error fetching Specialty lists', []);
        }
        return $this->sendResponse($specialtyLists, 'Specialty lists Fetched successfully.');

    }

}
