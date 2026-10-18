<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EbookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])
  ->middleware('throttle:5,1');

Route::post('/login', [AuthController::class, 'login'])
  ->middleware('throttle:api-login');

Route::post('/logout', [AuthController::class, 'logout'])
  ->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:sanctum');

Route::get('/ebooks', [EbookController::class, 'index'])
  ->middleware('auth:sanctum');
Route::post('/ebooks', [EbookController::class, 'store'])
  ->middleware('auth:sanctum');
Route::get('/ebooks/{id}/file', [EbookController::class, 'file'])
  ->whereNumber('id')
  ->middleware('auth:sanctum');
Route::patch('/ebooks/{id}', [EbookController::class, 'update'])
  ->whereNumber('id')
  ->middleware('auth:sanctum');
