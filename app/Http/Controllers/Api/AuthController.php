<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
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
