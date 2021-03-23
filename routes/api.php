<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestController;

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


// такой синтакчис требует обьявления use вверху файла
//Route::get('test', [TestController::class, 'index']);
// Или можно так, но при этом требуется полное имя файла контроллера
// В 8 версии laravel
Route::get('testdb', 'App\Http\Controllers\Api\TestController@testdb');
Route::get('hello', 'App\Http\Controllers\Api\TestController@index');
// очерерь и продолжительное время выполнения
Route::get('testQueue', 'App\Http\Controllers\Api\TestController@testQueue');
Route::get('testQueue1', 'App\Http\Controllers\Api\TestController@testQueue1');
// прогресс выполнения
Route::get('getProgress', 'App\Http\Controllers\Api\TestController@getProgress');

// Redis cashe
Route::post('redisSave', 'App\Http\Controllers\Api\TestController@redisSave');
Route::post('redisLoad', 'App\Http\Controllers\Api\TestController@redisLoad');

Route::get('client', 'App\Http\Controllers\Api\TestController@getClients');
Route::middleware('auth:api')->get('testdb1', 'App\Http\Controllers\Api\TestController@testdb1');

// тест сброса пароля
Route::post('password/email', 'App\Http\Controllers\Api\PasswordController@postEmail');
Route::post('password/reset', 'App\Http\Controllers\Api\PasswordController@postReset');
//Route::post('password/set', 'UserController@setPassword');

// Генерация события
Route::group([
    'middleware' => 'api',
    'prefix' => 'event'
], function () {
    Route::post('genEvent', 'App\Http\Controllers\Api\TestController@genEvent');
    Route::post('genEvent2', 'App\Http\Controllers\Api\TestController@genEvent2');
    Route::post('setBindExchange', 'App\Http\Controllers\Api\TestController@setBindExchange');
});




Route::get('/test1', function () {
    // Only authenticated users may access this route...
    return 'OK USER authetificated';
})->middleware('auth:api');



/* Добавить аутентификацию по JWT */
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
    ], function () {
//  При использовании защиты роутов перенаправление идет так
//  что надо править логин
//        Route::post('login', 'App\Http\Controllers\AuthController@login');
        Route::post('login',  ['as' => 'login', 'uses' =>'App\Http\Controllers\AuthController@login']);
// и добавлять это чтобы не выкидывал ошибку
        Route::get('login',  ['as' => 'login', 'uses' =>'App\Http\Controllers\AuthController@login']);

        Route::post('logout', 'App\Http\Controllers\AuthController@logout');
        Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');
        Route::post('me', 'App\Http\Controllers\AuthController@me');
// метод registration добавлен из статьи
// https://pacificsky.ru/frameworks/laravel/195-laravel-jwt-avtorizacija-cherez-access-token.html
// иначе как создать нового пользователя?
        Route::post('registration', 'App\Http\Controllers\AuthController@registration');
        Route::get('payload', 'App\Http\Controllers\AuthController@payload');
// поcлать письмо
        Route::get('sendmail', 'App\Http\Controllers\AuthController@sendEmail');
        Route::get('resetpassword', 'App\Http\Controllers\AuthController@sendResetLinkEmail');
        Route::get('resetpassword_link/{token}', 'App\Http\Controllers\AuthController@resetPassword');
        Route::post('resetpassword_link/{token}', 'App\Http\Controllers\AuthController@setPassword');

});
