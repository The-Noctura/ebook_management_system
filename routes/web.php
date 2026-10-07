<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EbookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  return view('welcome');
});


// ============ MIDDLEWARE UNTUK YANG BELUM LOGIN ==========
Route::middleware('guest')->group(function () {
  // ========== REGISTER ==========
  Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
  Route::post('/register', [AuthController::class, 'register'])->name('register.store');

  // ========== LOGIN ==========
  Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [AuthController::class, 'login'])->name('login.store')->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
  Route::get('/ebooks', [EbookController::class, 'index'])->name('ebooks.index');
  Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
