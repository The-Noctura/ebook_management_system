<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
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

  public function register(RegisterRequest $request): RedirectResponse
  {
    $validated = $request->validated();

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
