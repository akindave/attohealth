<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\OtpCode;
use Twilio\Rest\Client;
use App\Mail\ConfirmEmail;
use App\Models\KycList;
use App\Models\Referee;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\File;
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
            'user_category_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'mobile_number' => 'required|unique:users',
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
        $success['token'] =  $user->createToken('attohealth')->plainTextToken;
        $success['user'] =  $user;

        return $this->sendResponse($success, 'User register successfully.');

    }

    public function employeeDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'designation' => 'required',
            'gender' => 'required',
            'experience_year' => 'required',
            'education_level' => 'required',
            'certificate_of_practice' => 'required',
            'academic_certificate' => 'required',
            'resume' => 'required',
            'looking_for' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'address' => 'required',
            'identity_type' => 'required',
            'front_image' => 'required',
            'back_image' => 'required',
            'interview_time' => 'required',
            'interview_date' => 'required',
            'ref_one_name' => 'required',
            'ref_one_email' => 'required|email',
            'ref_one_mobile' => 'required',
            'ref_two_name' => 'required',
            'ref_two_email' => 'required|email',
            'ref_two_mobile' => 'required',

        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if ($request->file('certificate_of_practice') || $request->file('academic_certificate') || $request->file('resume')) {
            $image = $request->file('certificate_of_practice');
            $image2 = $request->file('academic_certificate');
            $image3 = $request->file('resume');

            $imagePath = $image->store('userdoc', 'public');
            $imagePath2 = $image2->store('userdoc', 'public');
            $imagePath3 = $image3->store('userdoc', 'public');
            // $filename = time() . rand() . '.' . $image->getClientOriginalExtension();
            // $imagePath = $image->store('receipts', 'public');
            // return $this->sendResponse($imagePath2, 'Reciept got here');
        } else {
            $base64File1 = $request->certificate_of_practice;
            $base64File2 = $request->academic_certificate;
            $base64File3 = $request->resume;
            // decode the base64 file
            $fileData1 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64File1));
            $fileData2 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64File2));
            $fileData3 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64File3));
            // save it to temporary dir first.
            $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
            $tmpFilePath2 = sys_get_temp_dir() . '/' . Str::uuid()->toString();
            $tmpFilePath3 = sys_get_temp_dir() . '/' . Str::uuid()->toString();

            file_put_contents($tmpFilePath, $fileData1);
            file_put_contents($tmpFilePath2, $fileData2);
            file_put_contents($tmpFilePath3, $fileData3);
            // this just to help us get file info.
            $tmpFile = new File($tmpFilePath);
            $tmpFile2 = new File($tmpFilePath2);
            $tmpFile3 = new File($tmpFilePath3);

            $file = new UploadedFile(
                $tmpFile->getPathname(),
                $tmpFile->getFilename(),
                $tmpFile->getMimeType(),
                0,
                true // Mark it as test, since the file isn't from real HTTP POST.
            );
            $file2 = new UploadedFile(
                $tmpFile2->getPathname(),
                $tmpFile2->getFilename(),
                $tmpFile2->getMimeType(),
                0,
                true // Mark it as test, since the file isn't from real HTTP POST.
            );
            $file3 = new UploadedFile(
                $tmpFile3->getPathname(),
                $tmpFile3->getFilename(),
                $tmpFile3->getMimeType(),
                0,
                true // Mark it as test, since the file isn't from real HTTP POST.
            );
            $imagePath = $file->store('userdoc', 'public');
            $imagePath2 = $file2->store('userdoc', 'public');
            $imagePath3 = $file3->store('userdoc', 'public');
        }

        $user = User::find($request->user_id)->update([
            'designation' => $request->designation,
            'gender' => $request->gender,
            'certificate_of_practice' => $imagePath,
            'academic_certificate' => $imagePath2,
            'resume' => $imagePath3,
            'experience_year' => $request->experience_year,
            'education_level' => $request->education_level,
            'looking_for' => $request->looking_for,
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'address' => $request->address,
            'interview_time' => $request->interview_time,
            'interview_date' => $request->interview_date
        ]);

        //this will take care of the kyx doc
        $saveIdentity = $this->createIdentity($request);

        if(!$saveIdentity){
            return $this->sendError('Error Submitting Kyc', []);
        }

        $saveRefferer = $this->saveRefferer($request);

        if(!$saveRefferer){
            return $this->sendError('Error Submitting Referers', []);
        }


        return $this->sendResponse([] , 'Registration completed successfully.');

    }

    public function createIdentity($request){

        if(KycList::whereUserId($request->user_id)->first()){
            return $this->sendError('kYC already submitted for this user', []);
        }else{
             try {
                // $image = ;
                // || $request->file('holding_image')
                if ($request->file('front_image') || $request->file('back_image')) {
                    $image = $request->file('front_image');
                    $image2 = $request->file('back_image');

                    $imagePath = $image->store('identity', 'public');
                    $imagePath2 = $image2->store('identity', 'public');
                    // $filename = time() . rand() . '.' . $image->getClientOriginalExtension();
                    // $imagePath = $image->store('receipts', 'public');
                    // return $this->sendResponse($imagePath2, 'Reciept got here');
                } else {
                    $base64File1 = $request->front_image;
                    $base64File2 = $request->back_image;
                    // decode the base64 file
                    $fileData1 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64File1));
                    $fileData2 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64File2));
                    // save it to temporary dir first.
                    $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
                    $tmpFilePath2 = sys_get_temp_dir() . '/' . Str::uuid()->toString();
                    file_put_contents($tmpFilePath, $fileData1);
                    file_put_contents($tmpFilePath2, $fileData2);
                    // this just to help us get file info.
                    $tmpFile = new File($tmpFilePath);
                    $tmpFile2 = new File($tmpFilePath2);

                    $file = new UploadedFile(
                        $tmpFile->getPathname(),
                        $tmpFile->getFilename(),
                        $tmpFile->getMimeType(),
                        0,
                        true // Mark it as test, since the file isn't from real HTTP POST.
                    );
                    $file2 = new UploadedFile(
                        $tmpFile2->getPathname(),
                        $tmpFile2->getFilename(),
                        $tmpFile2->getMimeType(),
                        0,
                        true // Mark it as test, since the file isn't from real HTTP POST.
                    );
                    $imagePath = $file->store('identity', 'public');
                    $imagePath2 = $file2->store('identity', 'public');
                }
                $identity = KycList::create([
                        'user_id' => $request->user_id,
                        'type' => $request->identity_type,
                        'identity_front' => $imagePath,
                        'identity_back' => $imagePath2,
                        // 'holding_image' => $imagePath2,
                        'status' => 'submitted'
                    ]);
                return true;
            } catch (\Throwable $th) {
                return $th;
            }
        }
    }

    public function saveRefferer($request){
        if(Referee::whereUserId($request->user_id)->count() == 2){

        }else{
            Referee::create([
                'user_id' => $request->user_id,
                'fullname' => $request->ref_one_name,
                'email' => $request->ref_one_email,
                'mobile' => $request->ref_one_mobile,
                'status' => 'submitted'
            ]);

            Referee::create([
                'user_id' => $request->user_id,
                'fullname' => $request->ref_two_name,
                'email' => $request->ref_two_email,
                'mobile' => $request->ref_two_mobile,
                'status' => 'submitted'
            ]);
        }

        return true;
    }

    public function employerDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'user_id' => 'required',
            'name_of_org' => 'required',
            'org_logo' => 'required|mimes:jpg,png,jpeg',
            'practicing_license' => 'required|mimes:jpg,pdf,doc,jpeg',
            'specialty' => 'required',
            'country' => 'required',
            'state' => 'required',
            'address' => 'required',

        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if ($request->file('practicing_license') || $request->file('org_logo')) {
            $image = $request->file('practicing_license');
            $image2 = $request->file('org_logo');

            $imagePath = $image->store('userdoc', 'public');
            $imagePath2 = $image2->store('userdoc', 'public');
            // $filename = time() . rand() . '.' . $image->getClientOriginalExtension();
            // $imagePath = $image->store('receipts', 'public');
            // return $this->sendResponse($imagePath2, 'Reciept got here');
        } else {
            $base64File1 = $request->practicing_license;
            $base64File2 = $request->org_logo;
            // decode the base64 file
            $fileData1 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64File1));
            $fileData2 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64File2));
            // save it to temporary dir first.
            $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
            $tmpFilePath2 = sys_get_temp_dir() . '/' . Str::uuid()->toString();

            file_put_contents($tmpFilePath, $fileData1);
            file_put_contents($tmpFilePath2, $fileData2);
            // this just to help us get file info.
            $tmpFile = new File($tmpFilePath);
            $tmpFile2 = new File($tmpFilePath2);

            $file = new UploadedFile(
                $tmpFile->getPathname(),
                $tmpFile->getFilename(),
                $tmpFile->getMimeType(),
                0,
                true // Mark it as test, since the file isn't from real HTTP POST.
            );
            $file2 = new UploadedFile(
                $tmpFile2->getPathname(),
                $tmpFile2->getFilename(),
                $tmpFile2->getMimeType(),
                0,
                true // Mark it as test, since the file isn't from real HTTP POST.
            );

            $imagePath = $file->store('userdoc', 'public');
            $imagePath2 = $file2->store('userdoc', 'public');
        }

        $user = User::find($request->user_id)->update([
            'name_of_org' => $request->name_of_org,
            'gender' => $request->gender,
            'specialty' => $request->specialty,
            'org_logo' => $imagePath,
            'practicing_license' => $imagePath2,
            'country' => $request->country,
            'state' => $request->state,
            'address' => $request->address,
        ]);


        return $this->sendResponse([] , 'Registration completed successfully.');

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
