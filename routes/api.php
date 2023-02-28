<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// account
Route::controller(AuthController::class)->group(function () {
    Route::post('auth/login', 'login');
    Route::post('auth/register/employee', 'registerEmployee');
    Route::post('auth/register/employer', 'registerEmployer');
    Route::post('confirm/email', 'confirmEmail');
    Route::post('verify/email', 'verifyEmail');
    Route::post('confirm/mobile', 'confirmMobile');
    Route::post('verify/mobile', 'verifyMobile');
    Route::post('check/email', 'checkEmail');
    Route::post('check/username', 'checkUsername');
    Route::post('auth/reset/password', 'resetPassword');
});
