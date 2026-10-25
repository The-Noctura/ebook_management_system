<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebEbookController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
  // Sudah login -> koleksi. Belum login -> login (nanti diganti landing page, butir 5).
  return Auth::check()
    ? redirect()->route('ebooks.index')
    : view('landing');
})->name('home');

// ============ MIDDLEWARE UNTUK YANG BELUM LOGIN ==========
Route::middleware('guest')->group(function () {
  // ========== REGISTER ==========
  Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
  Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.store');

  // ========== LOGIN ==========
  Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [AuthController::class, 'login'])->name('login.store')->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
  Route::get('/ebooks', [WebEbookController::class, 'index'])->name('ebooks.index');

  // C11: upload dan buka PDF. /ebooks/create didaftarkan sebelum route ber-{id}.
  Route::get('/ebooks/create', [WebEbookController::class, 'create'])->name('ebooks.create');
  Route::post('/ebooks', [WebEbookController::class, 'store'])->name('ebooks.store');
  Route::get('/ebooks/{id}/file', [WebEbookController::class, 'file'])
    ->whereNumber('id')
    ->name('ebooks.file');

  Route::delete('/ebooks/{id}', [WebEbookController::class, 'destroy'])
    ->whereNumber('id')
    ->name('ebooks.destroy');

  Route::patch('/ebooks/{id}', [WebEbookController::class, 'update'])
    ->whereNumber('id')
    ->name('ebooks.update');
  Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
