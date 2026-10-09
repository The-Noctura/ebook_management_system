<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])
  ->middleware('throttle:5,1');

Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:sanctum');
