<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\HiringList;
use App\Models\User;
use App\Models\ApplicantList;
use Illuminate\Support\Facades\Auth;

class EmployerController extends BaseController
{
    //
    public function create_hire_listing(Request $request){
         // return $request->all();
         $validator = Validator::make($request->all(), [
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'gender' => 'required',
            'offer_type' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'offer_amount' => 'required',
            'job_category_id' => 'required',
            'offer_amount_day_type' => 'required',
            'accommodation' => 'required',
            'number_of_hire' => 'required',
            'remark' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $createHire = HiringList::create([
            'user_id' => Auth::id(),
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'gender' => $request->gender,
            'offer_type' => $request->offer_type,
            'job_category_id' => $request->job_category_id,
            'from' => $request->from_date,
            'to' => $request->to_date,
            'offer_amount' => $request->offer_amount,
            'day_type' => $request->offer_amount_day_type,
            'accommodation' => $request->accommodation,
            'day_type' => $request->offer_amount_day_type,
            'number_hires' => $request->number_of_hire,
            'remark' => $request->remark,
            'status' => 'private'
        ]);
        if(!$createHire ){
            return $this->sendError('Error creating Hiring Listing', []);
        }
        return $this->sendResponse($createHire, 'Hiring Listing Created successfully.');



    }

    public function post_hiring_job(Request $request){

        $validator = Validator::make($request->all(), [
            'job_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $job_id = $request->job_id;

        $lookForHiring = HiringList::whereUserId(Auth::id())->whereId($job_id)->first();
        if (!$lookForHiring) {
            return $this->sendError('Error fetching Job or User Does Not Have Access', []);
        }
        $lookForHiring->status = "public";
        $lookForHiring->save();
        return $this->sendResponse($lookForHiring, 'Your Hiring has been successfully posted to Public!');
    }

    public function send_offer(Request $request){

        $validator = Validator::make($request->all(), [
            'job_id' => 'required',
            'applicant_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $job_id = $request->job_id;
        $applicant_id = $request->applicant_id;

        $fetchJobById  = HiringList::whereUserId(Auth::id())->whereId($job_id)->first();
        $checkForApplicant = User::whereId($applicant_id)->first();
        if (!$fetchJobById || !$checkForApplicant) {
            return $this->sendError('User Does Not Have Access to this Job or Invalid Applicant Id', []);
        }

        //check if the user has been offered already or not
        $checkIfApplicantHasBeenOffered = ApplicantList::whereApplicantId($applicant_id)
        ->whereJobId($job_id)
        ->first();
        if($checkIfApplicantHasBeenOffered){
            // if($checkIfApplicantHasBeenOffered->status == "applied"){
            //     $checkIfApplicantHasBeenOffered->status = "offered";
            //     $checkIfApplicantHasBeenOffered->save();
            // }else{
                return $this->sendError('This Job offer has already been sent to this user!', []);

        }


        //the fist thing to do is to check for the number of applicant they want
        if($fetchJobById->active_hires < $fetchJobById->number_hires){
            //then count the numbers of applicants on the list for the job both offer or hired
            $noOfOffer = ApplicantList::whereJobId($job_id)
            ->whereStatus('offered')
            ->orWhere('status','hired')
            ->count();
            if($noOfOffer == $fetchJobById->number_hires){
                return $this->sendError('Opps! cant send offer.You can only send offer based on your pre-selected number of hiring applicants', []);
            }

            $sendOffer = ApplicantList::create([
                'applicant_id' => $applicant_id,
                'job_id' => $job_id,
                'status' => 'offered'
            ]);

            if(!$sendOffer){
                return $this->sendError('Error sending offer! Please try again', []);
            }

            return $this->sendResponse($sendOffer, 'Offer Sent successfully.');

        }

        return $this->sendError('You have reached your hiring Limit', []);




    }


}
