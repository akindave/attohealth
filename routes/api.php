<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmployerController;
use App\Http\Controllers\Api\MisController as ApiMiscController;

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

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('employer')->group(function () {
        Route::controller(EmployerController::class)->group(function () {
           Route::post('create/hiring/listing','create_hire_listing');


        });
    });

});
Route::controller(ApiMiscController::class)->group(function () {
    Route::get('get/all/country', 'getAllCountry');
    Route::get('get/state/by/country/{id}', 'getStateByCountry');
    Route::get('get/city/by/state/{id}', 'getCityByState');
    Route::get('get/job/categories', 'getAllJobCategory');
    Route::get('get/user/by/designation/{designation_id}','getUserByDesignation');
});


// account
Route::controller(AuthController::class)->group(function () {
    Route::post('auth/login', 'login');
    Route::post('auth/register', 'register');
    Route::post('auth/complete/employee/registration', 'employeeDocument');
    Route::post('auth/complete/employer/registration', 'employerDocument');
    // Route::post('auth/register', 'getAllCountry');


    Route::post('confirm/email', 'confirmEmail');
    Route::post('verify/email', 'verifyEmail');
    Route::post('check/email', 'checkEmail');
    Route::post('auth/reset/password', 'resetPassword');

    // Route::post('confirm/mobile', 'confirmMobile');
    // Route::post('verify/mobile', 'verifyMobile');
    // Route::post('check/username', 'checkUsername');
});
