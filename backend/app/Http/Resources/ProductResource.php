<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'product_code' => $this->product_code,
            'name' => $this->name,
            'price' => $this->price,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'stock' => $this->stock,
            'status' => $this->status,
            
            'category' => new CategoryResource(
                $this->whenLoaded('category')
            ),

            'images' => ProductImageResource::collection($this->whenLoaded('images')),

            'specifications' => ProductSpecificationResource::collection($this->whenLoaded('specifications')),

            'variants' => ProductVariantResource::collection($this->whenLoaded('variants'))
        ];
    }
}
