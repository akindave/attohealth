<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\HiringList;
use App\Models\User;
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
            'from' => $request->from_date,
            'to' => $request->to_date,
            'offer_amount' => $request->offer_amount,
            'day_type' => $request->offer_amount_day_type,
            'accommodation' => $request->accommodation,
            'day_type' => $request->offer_amount_day_type,
            'number_hires' => $request->number_of_hire,
            'remark' => $request->remark,
            'status' => 'submitted'
        ]);
        if(!$createHire ){
            return $this->sendError('Error creating Hiring Listing', []);
        }
        return $this->sendResponse($createHire, 'Hiring Listing Created successfully.');



    }


}
