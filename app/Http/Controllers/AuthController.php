<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
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

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer', ]);
    }
}
