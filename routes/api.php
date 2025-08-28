<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthenticationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register',[AuthenticationController::class,'register']);
Route::post('/login',[AuthenticationController::class,'login']);

Route::middleware('auth:sanctum')->group( function(){
    Route::get('/me', [AuthenticationController::class,'userInfo']);
    Route::post('/logout',[AuthenticationController::class,'logOut']);
});




// Route::group(['namespace' => 'App\Http\Controllers\API'], function () {
//     // --------------- Register and Login ----------------//
//     Route::post('register', 'AuthenticationController@register')->name('register');
//     Route::post('login', 'AuthenticationController@login')->name('login');
    
//     // ------------------ Get Data ----------------------//
//     Route::middleware('auth:sanctum')->group(function () {
//         Route::get('get-user', 'AuthenticationController@userInfo')->name('get-user');
//         Route::post('logout', 'AuthenticationController@logOut')->name('logout');
//     });
// });