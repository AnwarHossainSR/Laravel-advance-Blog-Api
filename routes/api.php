<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotController;

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}); */
//Route::get('user', [AuthController::class,'authenticatedUser'])->middleware('auth:api');


//general
Route::post('login', [AuthController::class,'login']);
Route::post('register', [AuthController::class,'registration']);
Route::post('forgot',[ForgotController::class,'forgotPassword']);
Route::post('reset',[ForgotController::class,'resetReq']);

//authenticated
Route::middleware('auth:api')->group(function () {
    Route::get('user', [AuthController::class,'authenticatedUser']);

});
