<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(RegisterRequest  $request): \Illuminate\Http\JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => ['access_token' => $token, 'token_type' => 'Bearer'],
        ], 201);
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'errors' => [
                    [
                        'code' => 'user_not_found',
                        'message' => 'Пользователь не найден',
                    ],
                ],
                'message' => 'Пользователь не найден.',
                'meta' => null
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => [
                    [
                        'code' => 'invalid_password',
                        'message' => 'Неверный пароль',
                    ],
                ],
                'message' => 'Неверный пароль.',
                'meta' => null
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'data' => ['access_token' => $token, 'token_type' => 'Bearer'],
            'meta' => null
        ], 200);

    }

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'data' => null,
            'meta' => null
        ], 200);
    }
}
