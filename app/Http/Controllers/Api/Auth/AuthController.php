<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $user = User::create($validatedData);
            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'data' => $user,
            ], 201);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $ex->getMessage(),
            ], 500);
        }
    }
    public function login(LoginRequest $request)
    {
        try {
            $validatedData = $request->validated();

            if (!$token = auth()->attempt($validatedData)) {
                return response()->error('Invalid email or password.', 401);
            }
            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully',
                'token' => $token
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $ex->getMessage(),
            ], 500);
        }
    }
    public function me()
    {
        try {
            return response()->json([
                'status' => true,
                'message' => 'User data retrieved successfully',
                'data' => auth()->user()
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $ex->getMessage(),
            ], 500);
        }
    }
    public function refresh()
    {
        try {
            return response()->json([
                'status' => true,
                'message' => 'Token refreshed successfully',
                'token' => auth()->refresh()
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $ex->getMessage(),
            ], 500);
        }
    }
    public function logout()
    {
        try {
            auth()->logout();
            return response()->json([
                'status' => true,
                'message' => 'User logged out successfully',
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $ex->getMessage(),
            ], 500);
        }
    }
}
