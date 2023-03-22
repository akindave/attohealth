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

    public function listHireByOfferType($offer_type){
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
            return $this->sendError('Error fetching Job', []);
        }

        $getJobDetails = $getjob->with(['user' => function ($query){

        }])
        ->with('state')
        ->with('country')
        ->with('city')
        ->with('offer_type')
        ->get();


        // foreach ( $getJobDetails as  $getJobDetail){

        //     $markers = $this->calculateDistanceBetweenTwoAddresses( $getJobDetail->user->lat, $getJobDetail->user->long,Auth::user()->lat,Auth::user()->long);

        //     $getJobDetail['distance'] = $markers;
        // }

        return $this->sendResponse($getJobDetails, 'Job detail Fetched successfully.');

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

    public function myoffer(){
        $getmyoffers = ApplicantList::whereApplicantId(Auth::id())
        ->whereStatus('offered')
        ->with('hiringdetail')
        ->get();

        if(!$getmyoffers){
            return $this->sendError('Error getting your offer list', []);
        }
        return $this->sendResponse($getmyoffers, 'You offer Fetched successfully.');


    }

    public function acceptOffer(Request $request){

        $validator = Validator::make($request->all(), [
            'job_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $job_id = $request->job_id;

        //first check if the person has been sent offer truly
        $userwasoffered = ApplicantList::whereJobId($job_id)
        ->whereApplicantId(Auth::id())
        ->whereStatus('offered')
        ->first();

        if(!$userwasoffered){
            //proceed
            return $this->sendError('User does not have access to this job', []);
        }

        $userwasoffered->status="hired";
        $userwasoffered->save();

        //fetch the job details and perform some queries

        $getJob = HiringList::whereId($job_id)->first();

        $getJob->active_hires += 1;

        $getJob->save();

        return $this->sendResponse($getJob, 'Offer Accepted Successfully!');

    }

    public function rejectOffer(Request $request){
        $validator = Validator::make($request->all(), [
            'job_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $job_id = $request->job_id;

        //first check if the person has been sent offer truly
        $userwasoffered = ApplicantList::whereJobId($job_id)
        ->whereApplicantId(Auth::id())
        ->whereStatus('offered')
        ->first();

        if(!$userwasoffered){
            //proceed
            return $this->sendError('User does not have access to this job', []);
        }

        $userwasoffered->status="rejected";
        $userwasoffered->save();
        return $this->sendResponse([], 'Offer Rejected Successfully!');
    }

    public function myJobList(){
        $getmyjobs = ApplicantList::whereApplicantId(Auth::id())
        ->whereStatus('hired')
        ->with('hiringdetail')
        ->get();

        if(!$getmyjobs){
            return $this->sendError('Error getting your job list', []);
        }
        return $this->sendResponse($getmyjobs, 'Your Jobs Fetched successfully.');
    }

}
