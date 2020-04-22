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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

//Route::prefix('v1')->name('api.v1')->group(function() {
//    Route::get('version',function() {
//        return 'v1';
//    });
//});
//
//Route::prefix('v2')->name('api.v2')->group(function() {
//    Route::get('version',function() {
//        return 'v2';
//    });
//});

/** V1版本 */
Route::prefix('v1')->namespace('Api')->name('api.v1.')->group(function() {
    Route::get('version', function() {return 'this is version v1';});
//    Route::middleware('throttle:'.config('api.rate_limit.sign'))->group(function() {
        // 图片验证码
        Route::post('captchas', 'CaptchasController@store');
        // 短信验证码
        Route::post('verificationCodes', 'VerificationCodesController@store');
        // 用户注册
        Route::post('users','UsersController@store');
        // 第三方登录
        Route::post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')->where('social_type', 'weixin');
        // 登录
        Route::post('authorizations', 'AuthorizationsController@store');
        // 刷新token
        Route::put('authorizations/current', 'AuthorizationsController@update');
        // 删除token
        Route::delete('authorizations/current', 'AuthorizationsController@destroy');
//    });

    Route::middleware('throttle:'.config('api.rate_limit.access'))->group(function() {

    });
});


Route::prefix('v2')->name('api.v2.')->group(function() {
    Route::get('version', function() {
        return 'this is version v2';
    })->name('version');
});