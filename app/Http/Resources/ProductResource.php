<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public const COLUMNS = [
        ['field'=>'id','label'=>'#','sortable'=>true],
        ['field'=>'name','label'=>'Name'],
        ['field'=>'price','label'=>'Net price','type'=>'money'],
        ['field'=>'vat_rate','label'=>'VAT %'],
        ['field'=>'stock','label'=>'In stock'],
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'price'=> number_format($this->price,2,'.',' '),
            'vat'  => $this->vat_rate,
            'stock'=> $this->stock,
        ];
    }
}
