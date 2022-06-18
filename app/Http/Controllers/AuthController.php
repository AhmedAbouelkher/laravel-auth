<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{

    // register
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
        ]);

        $fields['password'] = bcrypt($fields['password']);

        $user = User::create($fields);

        $accessToken = $user->createToken('PAT')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'access_token' => $accessToken,
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $checkUserCredentials =  auth()->attempt($credentials);

        if (!$checkUserCredentials) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = auth()->user();

        $accessToken = $user->createToken('PAT');

        return response()->json([
            'user' => new UserResource($user),
            'access_token' => $accessToken->plainTextToken,
            'raw_token' => $accessToken,
        ]);
    }

    public function about()
    {
        $user = auth()->user();

        return response()->json([
            'user' => new UserResource($user),
        ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        // auth()->user()->token()->revoke();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
