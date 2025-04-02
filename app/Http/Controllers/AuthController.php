<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;

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

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer', ]);

    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);


        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'errors' => ['email' =>  ['Пользователь не найден'] ],
                'message' => 'Нет пользователя с таким email'
            ], 404);

        }

        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => ['password' =>  ['Неверный пароль'] ],
                'message' => 'Вы ввели неверный пароль'
            ], 422);
        }


        $token = $user->createToken('auth_token')->plainTextToken;


        return response()->json(['access_token' => $token, 'token_type' => 'Bearer', ]);

    }

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Выход из системы прошёл успешно'], 200);
    }
}
