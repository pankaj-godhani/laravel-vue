<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Validator;
class HomeController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return Auth::user();
        } else {
            return false;
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // If using Sanctum:
            $token = $user->createToken('authToken')->plainTextToken;

            // If using Passport:
            // $token = $user->createToken('authToken')->accessToken;

            return response()->json(['token' => $token], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function register(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6', // 'confirmed' expects a field named 'password_confirmation'
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Automatically log the user in after registration
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Generate token
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['token' => $token], 200); // Success response
        }

        return response()->json(['error' => 'Login after registration failed'], 500); // Handle login failure
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
