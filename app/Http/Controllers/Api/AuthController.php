<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\OtpCode;
use Twilio\Rest\Client;
use App\Mail\ConfirmEmail;
// use App\Models\WalletType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $credentials = $validator->validated();
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $success['token'] =  $user->createToken('attohealth')->plainTextToken;
            $success['user'] =  $user;
            return $this->sendResponse($success, 'User login successfully.');
        } else {


            $response = ['message' => 'invalid email or password'];
            return $this->sendError('Invalid email or password', $validator->errors());
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'last_name' => 'required',
            'first_name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'mobile_number' => 'required',
            'country' => 'required',
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['isVerified'] = true;
        $input['email_verified_at'] = Carbon::now();
        $user = User::create($input);
        $success['token'] =  $user->createToken('mycredly')->plainTextToken;
        $success['user'] =  $user;

        return $this->sendResponse($success, 'User register successfully.');

    }
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $user = User::whereEmail($request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
            return $this->sendResponse([], 'User register successfully.');
        } else {
            return $this->sendError('User with email not found', $validator->errors());
        }
    }

    public function confirmEmail(Request $request)
    {
        // return Str::random(5);
        /**
         * Store a receiver email address to a variable.
         */
        $reveiverEmailAddress = $request->email;

        // generate otp code for user to use
        // return $reveiverEmailAddress;

        $code = random_int(10000, 99999);

        // $otp = new OtpCode();
        // $otp->code = $code;
        // $otp->email = $request->email;
        $otp = OtpCode::create([
            'code' => $code,
            'email' => $request->email,
        ]);
        // $otp->save();
        return $otp;

        /**
         * Import the Mail class at the top of this page,
         * and call the to() method for passing the
         * receiver email address.
         *
         * Also, call the send() method to incloude the
         * HelloEmail class that contains the email template.
         */

        // try {
        //     //code...
        //     Mail::to($reveiverEmailAddress)->send(new ConfirmEmail($otp));
        //     return $this->sendResponse([], 'Otp sent successfully.');
        // } catch (\Throwable $th) {
        //     return $this->sendError('Unable to send otp', []);
        // }



        /**
         * Check if the email has been sent successfully, or not.
         * Return the appropriate message.
         */
    }
    public function verifyEmail(Request $request)
    {
        $code = OtpCode::whereEmail($request->email)->first();
        // return  $this->sendResponse($code->code, 'Email verified Successfully');
        if ($code  && $code->code == $request->code) {
            return $this->sendResponse([], 'Email verified Successfully');
        } else {
            return $this->sendError('Otp verification failed', []);
        }
    }

    public function checkUsername(Request $request)
    {
        $user = User::whereUsername($request->username)->first();

        if ($user) {
            return $this->sendError('Username unavailable', []);
        } else {
            return $this->sendResponse([], 'Username available');
        }
    }
    public function checkEmail(Request $request)
    {
        $user = User::whereEmail($request->email)->first();

        if ($user) {
            return $this->sendError('Email is already used', []);
        } else {
            return $this->sendResponse([], 'Email available');
        }
    }




}
