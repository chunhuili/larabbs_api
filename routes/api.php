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

        // 游客可以访问的接口

        // 某个用户的详情
        Route::get('users/{user}', 'UsersController@show');
        // 分类列表
        Route::get('categories', 'CategoriesController@index');
        // 某个用户发布的话题
        Route::get('users/{user}/topics', 'TopicsController@userIndex');
        // 话题列表，详情
        Route::resource('topics', 'TopicsController')->only([
            'index', 'show'
        ]);

        // 登录后可以访问的接口
        Route::middleware('auth:api')->group(function() {
            // 当前登录用户信息
            Route::get('user', 'UsersController@me');
            // 编辑登录用户信息
            Route::patch('user', 'UsersController@update');
            // 上传图片
            Route::post('images', 'ImagesController@store');
            // 发布话题
            Route::resource('topics', 'TopicsController')->only([
                'store', 'update', 'destroy'
            ]);
            // 发布回复
            Route::post('topics/{topic}/replies', 'RepliesController@store');
        });
//    });

    Route::middleware('throttle:'.config('api.rate_limit.access'))->group(function() {

    });
});


Route::prefix('v2')->name('api.v2.')->group(function() {
    Route::get('version', function() {
        return 'this is version v2';
    })->name('version');
});