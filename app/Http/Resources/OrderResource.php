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
            'id'       => $this->id,
            'status'   => $this->status->value,
            'total'    => number_format($this->total_gross,2,'.',' '),
            'items'    => $this->items->map(fn($i)=>[
                'product'=> $i->product->name,
                'qty'    => $i->qty,
                'price'  => $i->price,
            ]),
            'created'  => $this->created_at->toDateTimeString(),
        ];
    }
}
