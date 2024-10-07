<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
          
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['status' => 'success', 'token' => $token], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        
        $token = $user->createToken('authToken')->plainTextToken;
        return response()->json([
            'status' => 'success',
            'message' => 'Welcome ' . ucfirst($user->role),
            'role' => $user->role,
            'token' => $token,
        ], 200);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json(['status' => 'success', 'message' => 'Logged out'], 200);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        Password::sendResetLink($request->only('email'));
        return response()->json(['status' => 'success', 'message' => 'Password reset link sent.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['status' => 'success', 'message' => 'Password has been reset.'])
            : response()->json(['status' => 'error', 'message' => 'Failed to reset password.'], 500);
    }
}
