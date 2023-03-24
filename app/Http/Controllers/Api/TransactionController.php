<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class TransactionController extends BaseController{

    //
    public function verifyTransactionPin($pin){
        $confirmPin = User::whereId(Auth::id())->where('transaction_pin',$pin)->first();
        if($confirmPin){
            return $this->sendResponse($confirmPin, 'Pin Valid');
        }else{
            return $this->sendResponse([], 'Pin Invalid');
        }
    }

    public function withdrawFunds(Request $request)
    {
        try {
                $validator = Validator::make($request->all(), [
                    'amount' => 'required|numeric',
                    'bank_id' => 'required',
                    'account_number' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->sendError('Validation Error.', $validator->errors());
                }
                $credentials = $validator->validated();
                $credentials['user_id'] = Auth::id();

                $deductFromWallet = User::whereId(Auth::id())->first();
                $deduct  =  $deductFromWallet->balance-$request->amount;
                $deductFromWallet->balance = $deduct;
                $deductFromWallet->save();

                $withdrawal = Withdrawal::create($credentials);

                return $this->sendResponse($withdrawal, 'Withdrawal created successfully');

        } catch (\Throwable $th) {
            return $this->sendError('Unable to create Withdraw', $th);
        }
    }

}
