<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HiringList;
use App\Models\User;
use App\Models\ApplicantList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use DB;

class EmployeeController extends BaseController
{
    //

    public function listHireByOfferType($offer_type,$date){
        //get the hiring list base on the user selected location
        $activeUser = Auth::user();
        if(!$activeUser->state && !$activeUser->country){
            //give adequate error
            return $this->sendError('Error fetching user state or country!', []);
        }
        $getUserState = $activeUser->state;
        $getUserCountry = $activeUser->country;

        $getAllJobMatchingUserStateCountry = HiringList::whereState($getUserState)
        ->whereCountry($getUserCountry)
        ->whereOfferType($offer_type)
        ->with('user')
        ->with('state')
        ->with('country')
        ->with('city')
        ->whereDate('created_at',$date)
        ->get();

        if(!$getAllJobMatchingUserStateCountry){
            //return error
            return $this->sendError('Error fetching listing', []);
        }
        return $this->sendResponse($getAllJobMatchingUserStateCountry, 'Hiring Listing Fetched successfully.');

    }

    public function getJobDetail($job_id){
        $getjob = HiringList::find($job_id);

        if(!$getjob){
            //return an error
        }

        $getJobDetails = $getjob->with(['user' => function ($query){

        }])
        ->with('state')
        ->with('country')
        ->with('city')
        ->with('offer_type')
        ->get();


        foreach ( $getJobDetails as  $getJobDetail){

            $markers = $this->calculateDistanceBetweenTwoAddresses( $getJobDetail->user->lat, $getJobDetail->user->long,Auth::user()->lat,Auth::user()->long);

            $getJobDetail['distance'] = $markers;
        }

        return $this->sendResponse($getJobDetails, 'Job detail Fetched successfully.');

    }

    //this is gotten from the use of Haversine formula.
    public function calculateDistanceBetweenTwoAddresses($lat1, $lng1, $lat2, $lng2){
        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);

        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        $delta_lat = $lat2 - $lat1;
        $delta_lng = $lng2 - $lng1;

        $hav_lat = (sin($delta_lat / 2))**2;
        $hav_lng = (sin($delta_lng / 2))**2;

        $distance = 2 * asin(sqrt($hav_lat + cos($lat1) * cos($lat2) * $hav_lng));

        // 3959 for miles
        //6371 for km

        $distance = 3959*$distance;
        // If you want calculate the distance in miles instead of kilometers, replace 6371 with 3959.

        return $distance;
    }

    public function claimOffer(Request $request){

        $validator = Validator::make($request->all(), [

            'job_id' => 'required',

        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $userAlreadyApply = ApplicantList::whereApplicantId(Auth::id())->whereJobId($request->job_id)->exists();
        if($userAlreadyApply){
            return $this->sendError('You already Applied for this Job!', []);
        }
        $saveApplication = ApplicantList::create([
            'applicant_id' => Auth::id(),
            'job_id' => $request->job_id,
            'status' => 'applied'
        ]);

        if(!$saveApplication){
            return $this->sendError('Application not granted, Try again!!', []);
        }

        return $this->sendResponse([] , 'Application completed successfully.');

    }

}
