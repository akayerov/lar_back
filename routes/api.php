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
Route::middleware('auth:api')->get('testdb1', 'App\Http\Controllers\Api\TestController@testdb1');





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
});