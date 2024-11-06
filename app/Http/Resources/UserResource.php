<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * 
     * @OA\Schema(
     *     schema="UserResource",
     *     type="object",
     *     title="User Resource",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="John Doe"),
     *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time"),
     *     @OA\Property(property="orders", type="array", 
     *         @OA\Items(ref="#/components/schemas/OrderResource")
     *     )
     * )
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
