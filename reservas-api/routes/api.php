<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ReservationController;

Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);

// Services
Route::get('/services', [ServiceController::class, 'index']);
Route::post('/services', [ServiceController::class, 'store']);
Route::put('/services/{id}', [ServiceController::class, 'update']);
Route::delete('/services/{id}', [ServiceController::class, 'destroy']);

Route::get('/reservations', [ReservationController::class, 'index']); // todas
Route::post('/reservations', [ReservationController::class, 'store']); // crear
Route::post('/reservations/{id}/cancel', [ReservationController::class, 'cancel']); // cancelar
Route::get('/reservations/filter', [ReservationController::class, 'list']); // filtradas
