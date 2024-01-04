<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\RoleInPermissionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
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
});



Route::middleware(['keycloak.auth'])->group(function () {
    Route::middleware(['check.role:admin'])->group(function () {
        Route::post('/auth/register', [LoginController::class, 'register']);
        Route::put('/auth/update/{id}', [LoginController::class, 'update']);
        Route::delete('/auth/delete/{id}', [LoginController::class, 'destroy']);
        Route::post('/import/user', [UserController::class, 'importUser']);
        Route::post('/user/add-role', [RoleInPermissionController::class, 'addRolesInUser']);
        Route::post('/user/update-role/{id}', [RoleInPermissionController::class, 'updateRoleInUser']);
    });
    Route::middleware(['check.role:user'])->group(function () {
        Route::get('/auth/list', [LoginController::class, 'index']);
    });
});
