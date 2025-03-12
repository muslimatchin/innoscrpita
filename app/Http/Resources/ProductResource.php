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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'stock_quantity' => $this->stock_quantity,
            'published' => $this->published,
            'show_on_homepage' => $this->show_on_homepage,
            'notify_admin_for_quantity_below' => $this->notify_admin_for_quantity_below,
            'order_minimum_quantity' => $this->order_minimum_quantity,
            'order_maximum_quantity' => $this->order_maximum_quantity,
            'not_returnable' => $this->not_returnable,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
