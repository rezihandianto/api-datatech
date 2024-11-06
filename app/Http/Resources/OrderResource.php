<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * 
     * @OA\Schema(
     *     schema="OrderResource",
     *     type="object",
     *     title="Order Resource",
     *     description="Order resource representation",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="user_id", type="integer", example=1),
     *     @OA\Property(property="order_number", type="string", example="DT00001"),
     *     @OA\Property(property="total_amount", type="number", format="integer", example=150000),     
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time"),
     *     @OA\Property(
     *         property="user",
     *         type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="John Doe"),
     *         @OA\Property(property="email", type="string", example="john@example.com"),
     *         @OA\Property(property="age", type="integer", example=25),
     *         @OA\Property(property="membership_status", type="boolean", example="false"),
     *         @OA\Property(property="created_at", type="string", format="date-time"),
     *         @OA\Property(property="updated_at", type="string", format="date-time")
     *     )
     * )
     *
     * @return array<string, mixed>
     */    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
