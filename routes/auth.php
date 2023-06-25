<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Oauth\GoogleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function ()
{
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::get('google', [GoogleController::class, 'redirectToGoogle']);
    Route::get('google/callback', [GoogleController::class, 'handleGoogleCallback']);

    Route::group(['middleware' => ['auth:api']], function() {
        Route::get('/', [AuthController::class, 'isAuth'])->name('auth');
        Route::get('logout', [AuthController::class, 'revoke'])->name('logout');
    });
});

Route::group(['prefix' => 'user', 'middleware' => ['auth:api']], function ()
{
    Route::get('/', [UserController::class, 'index'])->name('user.index');
    Route::post('add', [UserController::class, 'store'])->name('user.add');
    Route::get('{id}', [UserController::class, 'show'])->name('user.show');
    Route::put('{id}/edit', [UserController::class, 'update'])->name('user.update');
});

Route::group(['prefix' => 'role', 'middleware' => ['auth:api']], function ()
{
    Route::get('/', [RoleController::class, 'index'])->name('route.index');
    Route::get('/{id}', [RoleController::class, 'show'])->name('route.show');
    Route::put('/{id}/update', [RoleController::class, 'update'])->name('route.update');
});