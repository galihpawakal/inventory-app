<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductController::class, 'apiIndex']);
Route::post('/products', [ProductController::class, 'store']);
Route::post('/products/{product}/sell', [ProductController::class, 'sell']);


Route::get('/users', [UserController::class, 'apiIndex']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{user}/change-role', [UserController::class, 'changeRole']);
Route::get('/roles', [UserController::class, 'RolesData']);
