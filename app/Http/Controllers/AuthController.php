<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
  // ========== REGISTER ==========
  public function showRegisterForm()
  {
    return view('register.form');
  }

  public function register(Request $request): RedirectResponse
  {
    if (is_string($request->input('email'))) {
      $request->merge([
        'email' => strtolower(trim($request->input('email'))),
      ]);
    }

    $validated = $request->validate([
      'name' => 'required|string|max:100',
      'email' => 'required|string|email|max:255|unique:users,email',
      'password' => 'required|string|confirmed|min:8',
    ], [
      'name.required' => 'Nama wajib diisi.',
      'name.max' => 'Nama maksimal 100 karakter.',
      'email.required' => 'Email wajib diisi.',
      'email.email' => 'Format email tidak valid.',
      'email.max' => 'Email maksimal 255 karakter.',
      'email.unique' => 'Email sudah digunakan.',
      'password.required' => 'Password wajib diisi.',
      'password.min' => 'Password minimal 8 karakter.',
      'password.confirmed' => 'Konfirmasi password tidak cocok.',
    ]);

    User::create([
      'name' => $validated['name'],
      'email' => $validated['email'],
      'password' => $validated['password'],
    ]);

    return redirect('/login')->with('status', 'Akun berhasil dibuat. Silakan masuk.');
  }

  // ========== LOGIN ==========
  public function showLoginForm()
  {
    return view('login.form');
  }

  public function login(Request $request): RedirectResponse
  {
    if (is_string($request->input('email'))) {
      $request->merge([
        'email' => strtolower(trim($request->input('email'))),
      ]);
    }

    $credentials = $request->validate([
      'email' => ['required', 'string', 'email'],
      'password' => ['required', 'string'],
    ], [
      'email.required' => 'Email wajib diisi.',
      'email.email' => 'Format email tidak valid.',
      'password.required' => 'Password wajib diisi.',
    ]);

    if (! Auth::attempt($credentials)) {
      return back()
        ->withErrors(['email' => 'Email atau password salah.'])
        ->onlyInput('email');
    }

    $request->session()->regenerate();

    return redirect()->intended('/ebooks');
  }

  public function logout(Request $request): RedirectResponse
  {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
  }
}
