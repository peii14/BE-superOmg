<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,            
            'description' => $this->detail,
            'image' => $this->image,           
            'price' => $this->price,            
            'stock' => $this->stock > 0 ? ($this->stock) : "Out of stock",            
            'discount' => $this->discount,
            'discountPrice' => round(((1-($this->discount/100))*$this->price),2),           

        ];
    }
}