<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthAdminController  extends Controller
{
    public function register(Request $request)
    {
        // Registration logic here
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'type' => 'admin',
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->assignRole($user->type);
        return response()->json([
            'status' => true,
            'message' => 'Admin registered successfully',
            // 'access_token' => $token,
            'user' => $user,
        ], 201);
    }
    public function login(Request $request)
    {
        // Login logic here
        $data = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);
        if (!Auth::attempt($data)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }
        $user = $request->user();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'status' => true,
            'message' => 'Admin logged in successfully',
            'access_token' => $token,
            'user' => $user,
        ], 200);
    }
    public function logout(Request $request)
    {
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $request->user()->currentAccessToken();
        $token->delete();
        return response()->json([
            'status' => true,
            'message' => 'Admin logged out successfully'
        ], 200);
    }
    public function me(Request $request)
    {
        return response()->json([
            'status' => true,
            'user' => $request->user()
        ], 200);
    }
    public function accsessToken(Request $request)
    {
        $user = $request->user();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'status' => true,
            'access_token' => $token,
        ], 200);
    }
    public function refreshToken(Request $request)
    {
        $user = $request->user();
        // Revoke all tokens...
        $user->tokens()->delete();
        // Create a new token
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'status' => true,
            'access_token' => $token,
        ], 200);
    }
}
