<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  public function login(LoginRequest $request): JsonResponse
  {
    $validated = $request->validated();
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

  public function register(RegisterRequest $request): JsonResponse
  {
    $validated = $request->validated();

    $user = User::create([
      'name' => $validated['name'],
      'email' => $validated['email'],
      'password' => Hash::make($validated['password']),
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
