<?php

use App\Events\PostCardProcessed;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/', [UserController::class, 'index'])->name('index');
Route::post('/upload', [UserController::class, 'upload'])->name('upload');



Route::get('/login', [LoginController::class, 'loginView'])->name('login');
Route::post('/auth/login', [LoginController::class, 'loginUser'])->name('auth.login');
