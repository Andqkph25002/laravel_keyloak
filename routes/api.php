<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();

    // validate

    // lưu mysql

    // api login

    // call api keycloak
});

// login(){
//     // check database if token not exprire

// }








Route::post('/auth/login', [LoginController::class, 'login']);
Route::post('/auth/register', [LoginController::class, 'register']);
Route::put('/auth/update/{id}' ,[LoginController::class , 'update']);
Route::delete('/auth/delete/{id}'  , [LoginController::class , 'destroy']);