<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  public function login(Request $request): JsonResponse
  {
    if (is_string($request->input('email'))) {
      $request->merge([
        'email' => strtolower(trim($request->input('email'))),
      ]);
    }

    $validated = $request->validate([
      'email' => 'required|string|email|max:255',
      'password' => 'required|string',
      'device_name' => 'sometimes|string|max:255',
    ]);

    $user = User::query()->where('email', $validated['email'])->first();

    if (! $user || ! Hash::check($validated['password'], $user->password)) {
      return response()->json([
        'message' => 'Email atau password salah.',
        'code' => 'invalid_credentials',
      ], 401);
    }

    $token = $user->createToken($validated['device_name'] ?? 'android')->plainTextToken;

    return response()->json([
      'data' => [
        'token' => $token,
        'token_type' => 'Bearer',
        'user' => [
          'id' => $user->id,
          'name' => $user->name,
          'email' => $user->email,
        ],
      ],
    ]);
  }

  public function register(Request $request): JsonResponse
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

    $user = User::create([
      'name' => $validated['name'],
      'email' => $validated['email'],
      'password' => $validated['password'],
    ]);

    return response()->json([
      'data' => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
      ],
    ], 201);
  }
}
