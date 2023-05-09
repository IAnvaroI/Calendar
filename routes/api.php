<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
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

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->middleware('auth.jwt');
});

Route::controller(UserController::class)->prefix('user')->middleware('auth.jwt')->group(function () {
    Route::get('/edit', 'edit');
    Route::patch('/update', 'update');
    Route::patch('/update/password', 'updatePassword');
    Route::delete('/delete', 'destroy');
});
