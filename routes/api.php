<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventsFiltersController;
use App\Http\Controllers\JWTController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth.jwt')->group(function () {
    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::get('/edit', 'edit');
        Route::patch('/update', 'update');
        Route::patch('/update/password', 'updatePassword');
        Route::delete('/delete', 'destroy');
    });

    Route::controller(EventController::class)->prefix('events')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{event}/edit', 'edit');
        Route::patch('/{event}', 'update');
        Route::delete('/{event}', 'destroy');
    });

    Route::get('/sharing-token', [JWTController::class, 'generateToken']);
});

Route::middleware('shared.jwt')->group(function () {
    Route::get('/shared/events', [EventController::class, 'sharedIndex']);
});

Route::get('/tags', [TagController::class, 'index']);
