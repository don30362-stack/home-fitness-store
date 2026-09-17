<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_no' => $this->order_no,

            'purchaser' => [
                'name' => $this->purchaser_name,
                'phone' => $this->purchaser_phone,
                'email' => $this->purchaser_email,
            ],

            'recipient' => [
                'name' => $this->recipient_name,
                'phone' => $this->recipient_phone,
                'postal_code' => $this->postal_code,
                'city' => $this->city,
                'district' => $this->district,
                'address' => $this->address,
            ],

            'shipping_method' => $this->shipping_method,
            'shipping_fee' => $this->shipping_fee,

            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'order_status' => $this->order_status,

            'subtotal' => $this->subtotal,
            'total_amount' => $this->total_amount,

            'logistics_company' => $this->logistics_company,
            'tracking_number' => $this->tracking_number,

            'items' => OrderItemResource::collection(
                $this->whenLoaded('items')
            ),

            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
