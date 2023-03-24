<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class SettingController extends BaseController{
    //

    public function createPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|string|max:6|min:6',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $user = User::find(Auth::id());
        $user->transaction_pin = $request->pin;
        $user->has_pin = 1;
        $user->save();
        $user = User::findOrFail(Auth::id());
        return $this->sendResponse($user, 'Pin updated successfully');
    }
    public function updatePin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_pin'=>'required|string|max:6|min:6',
            'pin' => 'required|string||max:6|min:6',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $user = User::find(Auth::id());
        if($user->transaction_pin === $request->old_pin){
            $user->transaction_pin = $request->pin;
            $user->save();
            $user = User::findOrFail(Auth::id());
            return $this->sendResponse($user, 'Pin updated successfully');
        }else{
            return $this->sendError('Invalid old pin', []);
        }
    }

    public function updateProfileInfo(Request $request){
        $user = User::find(Auth::id());
        // $validator = Validator::make($request->all(), [
        //     'last_name' => 'required',
        //     'first_name' => 'required',
        //     'username' => 'required|unique:users',
        //     'email' => 'required|email|unique:users',
        //     'password' => 'required|min:8',
        //     'mobile_number' => 'required',
        //     'country' => 'required',
        //     'code' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return $this->sendError('Validation Error.', $validator->errors());
        // }
        $input = $request->all();
       $newUser =  $user->update($input);
        return $this->sendResponse($newUser, 'User updated successfully');
    }
}
