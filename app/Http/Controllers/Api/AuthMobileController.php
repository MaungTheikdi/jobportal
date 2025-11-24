<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class AuthMobileController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        // Validation rules that match your Android RegisterRequest.java
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Return 422 validation error
        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // Note: We are not including phone, role, address here
            // to match the simple RegisterRequest.java we created.
            // You can add them back if you update your Android app.
        ]);

        // Create a token for the new user
        $token = $user->createToken('auth-token-for-' . $user->name)->plainTextToken;

        // Return the user and token
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token
        ], 201); // 201 Created
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Return 422 validation error
        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Attempt to log the user in
        if (!Auth::attempt($request->only('email', 'password'))) {
            // If authentication fails
            return response()->json([
                'message' => 'Invalid login details'
            ], 401); // 401 Unauthorized
        }

        // Get the authenticated user
        $user = User::where('email', $request['email'])->firstOrFail();

        // Create a new token
        $token = $user->createToken('auth-token-for-' . $user->name)->plainTextToken;

        // Return the user and token
        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ], 200); // 200 OK
    }
}