<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //REGISTER
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
        ]);
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $response = [
            'user' => $user,
        ];
        return response($response, 201);
    }

    //LOGIN
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        //check if user exists
        $user = User::where('email', $fields['email'])->first();
        //check for password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'invalid user'
            ], 401);
        }
        //create access token
        $token = $user->createToken('API_TOKEN')->plainTextToken;
        $response = [
            'user' => $user,
            'API_TOKEN' => $token,
        ];
        return response($response, 201);
    }

    //LOGOUT
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return response(
            [
                'message' => 'logged out'
            ]
        );
    }
}
