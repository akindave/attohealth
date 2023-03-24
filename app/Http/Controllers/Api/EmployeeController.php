<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HiringList;
use App\Models\User;
use App\Models\ApplicantList;
use App\Models\AttendanceList;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use DB;

class EmployeeController extends BaseController
{
    //

    public function listHireByOfferType($offer_type,$job_category_id,$per_page){
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
        ->whereJobCategoryId($job_category_id)
        ->with('user')
        ->with('state')
        ->with('country')
        ->with('city')
        ->paginate($per_page);

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
        ->first();


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

    public function myoffer($per_page){
        $getmyoffers = ApplicantList::whereApplicantId(Auth::id())
        ->whereStatus('offered')
        ->with('hiringdetail')
        ->paginate($per_page);

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

    public function myJobList($per_page){
        $getmyjobs = ApplicantList::whereApplicantId(Auth::id())
        ->whereStatus('hired')
        ->with('hiringdetail')
        ->paginate($per_page);

        if(!$getmyjobs){
            return $this->sendError('Error getting your job list', []);
        }
        return $this->sendResponse($getmyjobs, 'Your Jobs Fetched successfully.');
    }

    public function clock_in(Request $request){
        $validator = Validator::make($request->all(), [
            'job_id' => 'required',
            'date' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
         //first check if the person has been employed truly
         $getjob = HiringList::whereId($request->job_id)->first();

         if(!$getjob){
             //proceed
             return $this->sendError('Cant fetch job', []);
         }
         //check if the person is still hired
         $userIsHired = ApplicantList::whereApplicantId(Auth::id())
         ->whereJobId($getjob->id)
         ->whereStatus('hired')->exists();
         if(!$userIsHired){
            return $this->sendError('Sorry you are currently not hired for this job', []);
         }

         $job_expected_starting_date = $getjob->from;
         $job_expected_ending_date = $getjob->to;

         $date1 = Carbon::createFromFormat('d-m-Y', $request->date);
         $datefrom = Carbon::createFromFormat('d-m-Y', $job_expected_starting_date );
         $dateto = Carbon::createFromFormat('d-m-Y', $job_expected_ending_date );

         if(($date1->gte($datefrom)) && ($date1->lte($dateto))){

            //check if users has clocked in already
            $userCountryId = Auth::user()->country;
            $getCountry = Country::whereId($userCountryId)->first();
            $userTimeZone  = json_decode($getCountry->timezones);

            if(!$userCountryId || !$getCountry || empty($userTimeZone[0]->zoneName)){
                return $this->sendError('Error getting the user country or time zone', []);
            }
            $hasClockedInForDate = AttendanceList::whereJobId($request->job_id)
            ->whereUserId(Auth::id())
            ->where('date',$request->date)->exists();
            if($hasClockedInForDate){
                return $this->sendError('You already Clocked In for this date', []);
            }

            $storeAttendance = AttendanceList::create([
                    'date' => $request->date,
                    'user_id' => Auth::id(),
                    'job_id' => $request->job_id,
                    'clock_in_time' => Carbon::now()->tz($userTimeZone[0]->zoneName),
                    'status' => 'active'
            ]);

            if(!$storeAttendance){
                return $this->sendError('Error clocking in at the moment! Try again please.', []);
            }
            return $this->sendResponse($storeAttendance, 'Job Clocked in and marked as active');
         }

         return $this->sendError('The Job you are trying to clocking into may have been expired or closed', []);

    }
    public function clock_out(Request $request){
        $validator = Validator::make($request->all(), [
            'clock_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
         //first check if the person has been employed truly
         $getAtendance = AttendanceList::whereId($request->clock_id)->first();

         $getjob = HiringList::whereId($getAtendance->job_id)->first();


         if(!$getAtendance){
             //proceed
             return $this->sendError('Invalid Clock Id', []);
         }
         //check if the person is still hired
         $userIsHired = ApplicantList::whereApplicantId(Auth::id())
         ->whereJobId($getjob->id)
         ->whereStatus('hired')->exists();
         if(!$userIsHired){
            return $this->sendError('Sorry you are currently not hired for this job', []);
         }

         $job_expected_starting_date = $getjob->from;
         $job_expected_ending_date = $getjob->to;

         $date1 = Carbon::createFromFormat('d-m-Y', $getAtendance->date);
         $datefrom = Carbon::createFromFormat('d-m-Y', $job_expected_starting_date );
         $dateto = Carbon::createFromFormat('d-m-Y', $job_expected_ending_date );

         if(($date1->gte($datefrom)) && ($date1->lte($dateto))){

            //check if users has clocked in already
            $userCountryId = Auth::user()->country;
            $getCountry = Country::whereId($userCountryId)->first();
            $userTimeZone  = json_decode($getCountry->timezones);

            if(!$userCountryId || !$getCountry || empty($userTimeZone[0]->zoneName)){
                return $this->sendError('Error getting the user country or time zone', []);
            }


            $storeAttendance = $getAtendance->update([
                    'clock_out_time' => Carbon::now()->tz($userTimeZone[0]->zoneName),
                    'status' => 'completed'
            ]);

            if(!$storeAttendance){
                return $this->sendError('Error clocking out at the moment! Try again please.', []);
            }
            return $this->sendResponse([], 'Job Clocked out and marked as completed');
         }

         return $this->sendError('The Job you are trying to clocking out of may have been expired or closed', []);

    }



    public function checkIfDateHasNotpast($date1,$date2){
    }
}
