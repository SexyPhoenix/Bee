<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['namespace' => 'Bee', 'middleware' => ['auth.client']], function(){

	//UserController
	Route::get('getpermission', 'UserController@getUserPermission');
	Route::get('getdeptlist', 'UserController@filterDept');
	Route::get('getuserlist', 'UserController@filterUser');
	Route::post('uploadfile', 'UserController@fileUpload');
    Route::get('getaddress', 'UserController@getRegion');
    
	//TypeController
	Route::get('type', 'TypeController@index');
    Route::get('year', 'TypeController@getYear');
    Route::get('rest', 'TypeController@getRest');

    //WorkDayController
    Route::get('getdates', 'WorkDayController@getDateList');
    
    //ApplyController
    Route::get('apply/list', 'ApplyController@index');
    Route::post('apply/save', 'ApplyController@store');
    Route::post('apply/edit', 'ApplyController@update');
    Route::get('apply/delete', 'ApplyController@destroy');
    Route::post('flow/apply/cancel', 'ApplyController@cancel');

    //ApplyImportController
    Route::post('import', 'ApplyImportController@import');
    Route::post('apply/saveimport', 'ApplyImportController@storeImport');
    
    //ApplyLockController
    Route::post('apply/lock', 'ApplyLockController@store');

    //ChargeController
    Route::get('flow/charge', 'ChargeController@index');
    Route::post('flow/charge/agree', 'ChargeController@agree');
    Route::post('flow/charge/reject', 'ChargeController@reject');

    //AttendController
    Route::get('flow/attend', 'AttendController@index');
    Route::post('flow/attend/agree', 'AttendController@agree');
    Route::post('flow/attend/reject', 'AttendController@reject');

    //AffairsController
    Route::get('flow/affairs', 'AffairsController@index');
    Route::post('flow/affairs/agree', 'AffairsController@agree');
    Route::post('flow/affairs/reject', 'AffairsController@reject');     
});

Route::post('sso', 'Bee\UserController@login');
