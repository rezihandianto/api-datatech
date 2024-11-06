<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\StoreOrderRequest;
use App\Http\Requests\Api\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Get all orders",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/OrderResource")
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Something went wrong"),
     *             @OA\Property(property="error", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $order = Order::with('user')->paginate($perPage);
            return OrderResource::collection($order);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $ex->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Create new order",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"total_amount"},
     *             @OA\Property(property="total_amount", type="integer", example=100000),     
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/OrderResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\AdditionalProperties(
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Something went wrong"),
     *             @OA\Property(property="error", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function store(StoreOrderRequest $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            $validatedData['user_id'] = auth()->user()->id;
            $validatedData['order_number'] = Order::autonumber();
            $order = Order::create($validatedData);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Order created successfully',
                'data' => new OrderResource($order),
            ], 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $ex->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * 
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Get order details",
     *     description="Get details of a specific order",
     *     operationId="showOrder",
     *     tags={"Orders"},     
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data", type="object",
     *                 ref="#/components/schemas/OrderResource"
     *             )
     *         )
     *     ),     
     * @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Order not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Something went wrong"),
     *             @OA\Property(property="error", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        try {
            $order = Order::with('user')->find($id);
            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found',
                ], 404);
            }
            return new OrderResource($order);
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
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     summary="Update order",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"total_amount"},
     *             @OA\Property(property="total_amount", type="number", example=1000000),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/OrderResource")
     *         )
     *     ),
     *   @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error."),
     *            @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\AdditionalProperties(
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",     
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Order not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Something went wrong"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function update(UpdateOrderRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            $order = Order::find($id);
            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found',
                ], 404);
            }
            $order->update($validatedData);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Order updated successfully',
                'data' => new OrderResource($order),
            ], 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $ex->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     summary="Delete order",
     *     description="Delete specific order by id",
     *     operationId="deleteOrder",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Order not found")
     *         )
     *     ),
     *   @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",     
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),     
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Something went wrong"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $order = Order::find($id);
            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found',
                ], 404);
            }
            $order->delete();
            return response()->json([
                'status' => true,
                'message' => 'Order deleted successfully',
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
