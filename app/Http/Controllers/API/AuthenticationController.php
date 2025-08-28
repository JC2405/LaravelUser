<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\User;


class AuthenticationController extends Controller
{
    //Registrar Nueva Cuenta

    public function register(Request $request)
    {
        try {
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

            return response()->json([
                'response_code' => 201,
                'status' => 'success',
                'message' => 'User registered successfully.'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'response_code' => 422,
                'status' => 'error',
                'message'=>'Validation failed',
                'errors' => $e->errors(),
            ],500);
        }
    }



    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'response_code' => 401,
                    'status' => 'error',
                    'message' => 'Invalid credentials.'
                ], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'response_code' => 200,
                'status' => 'success',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'response_code' => 422,
                'status' => 'error',
                'message'=>'Validation failed',
                'errors' => $e->errors(),
            ],500);
        }
    }



    public function userInfo()
    {
        try {
            $users = User::latest()->paginate(10);
            return response()->json([
                'response_code' => 200,
                'status' => 'success',
                'message' => 'User information retrieved successfully.',
                'data' => $users
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'response_code' => 500,
                'status' => 'error',
                'message' => 'An error occurred while retrieving user information.',
                'errors' => $th->getMessage(),
            ], 500);
        }
    }


    public function logOut(Request $request)
    {
        try {
            $user = $request->user();

            if($user){
                $user->tokens()->delete();

                return response()->json([
                    'response_code' => 200,
                    'status' => 'success',
                    'message' => 'User logged out successfully.'
                ]);
            }
            return response()->json([
                'response_code' => 401,
                'status' => 'error',
                'message' => 'User not authenticated.'
            ], 401);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json([
                'response_code' => 500,
                'status' => 'error',
                'message' => 'An error occurred while logging out.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
