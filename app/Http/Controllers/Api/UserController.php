<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\StoreUserRequest;
use App\Http\Requests\Api\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $user = User::with('orders')->paginate($perPage);

            return UserResource::collection($user);
        } catch (\Exception $ex) {
            return response()->error($ex->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            $user = User::create($validatedData);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'user' => new UserResource($user),
            ], 201);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $ex->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ], 404);
            }
            return new UserResource($user);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $ex->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ], 404);
            }
            $user->update($validatedData);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'User updated successfully',
                'user' => $user,
            ], 200);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $ex->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ], 404);
            }
            $user->delete();
            return response()->json([
                'status' => true,
                'message' => 'User deleted successfully',
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $ex->getMessage(),
            ], 500);
        }
    }
}
