<?php

use App\Http\Controllers\Api\RoleController;
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

Route::middleware('keycloak.auth')->group(function () {
    Route::middleware('check.role:admin')->group(function () {
        Route::post('/user', [UserController::class, 'register']);
        Route::put('/user/{id}', [UserController::class, 'update']);
        Route::post('/users/import', [UserController::class, 'importUser']);
        Route::post('/user/{id}/assign-role', [RoleController::class, 'addRolesInUser']);
        Route::post('/user/{id}/sync-role', [RoleController::class, 'updateRoleInUser']);
    });
    Route::middleware('check.role:user')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
    });
});
Route::delete('/user/{id}', [UserController::class, 'destroy']);
