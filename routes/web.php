<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'bee', 'namespace' => 'Bee'], function() {

    
    Route::get('/', 'BeeController@index');
    Route::get('type', 'TypeController@index');
    Route::get('year', 'TypeController@getYear');
    Route::get('rest', 'TypeController@getRest');

    Route::get('getweekdays', 'WorkDayController@getWeekDays');
    Route::get('getmonthdays', 'WorkDayController@getMonthDays');
    Route::get('getdates', 'WorkDayController@getDateList');

    Route::post('sso', 'UserController@login');
    Route::get('getuserinfo', 'UserController@getUser');
    Route::get('getuserlist', 'UserController@filterUser');
    Route::get('getdeptlist', 'UserController@filterDept');
    Route::get('getpermission', 'UserController@getUserPermission');
    Route::get('getaddress', 'UserController@getRegion');

    Route::post('uploadfile', 'UserController@fileUpload');

    Route::get('apply/list', 'ApplyController@index');
    Route::get('apply/list', 'ApplyController@index');
    Route::post('apply/save', 'ApplyController@store');
    Route::post('apply/edit', 'ApplyController@update');
    Route::get('apply/delete', 'ApplyController@destroy');
    Route::post('flow/apply/cancel', 'ApplyController@cancel');

    Route::post('import', 'ApplyImportController@import');
    Route::post('apply/saveimport', 'ApplyImportController@storeImport');
    
    Route::post('apply/lock', 'ApplyLockController@store');

    Route::get('flow/charge', 'ChargeController@index');
    Route::post('flow/charge/agree', 'ChargeController@agree');
    Route::post('flow/charge/reject', 'ChargeController@reject');

    Route::get('flow/attend', 'AttendController@index');
    Route::post('flow/attend/agree', 'AttendController@agree');
    Route::post('flow/attend/reject', 'AttendController@reject');

    Route::get('flow/affairs', 'AffairsController@index');
    Route::post('flow/affairs/agree', 'AffairsController@agree');
    Route::post('flow/affairs/reject', 'AffairsController@reject');   
});