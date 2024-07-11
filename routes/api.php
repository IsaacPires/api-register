<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;

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

Route::post('user/register', [UserController::class, 'register']);
Route::post('user/login', [UserController::class, 'login']);
Route::get('/teste', function(){
    echo 'Hi';
});
Route::group(['middleware' => 'jwt.verify'], function () {

    Route::delete('user/delete/{id}', [UserController::class, 'delete']);
    Route::apiResource('client', ClientController::class);
});


