<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Auth::loginUsingId(1);
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*Route::post('login', array('uses' => 'HomeController@doLogin'));
Route::get('login', array('uses' => 'HomeController@showLogin')); */
Route::delete('/meds/{id?}', 'MedsController@destroy');
Route::put('/meds/{id}', 'MedsController@update');
Route::get('/meds/{id?}', 'MedsController@show');
Route::post('/meds', 'MedsController@store');

Route::resource('/medgroup', 'MedGroupController');
Route::resource('/medusage', 'MedUsageController');
