<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Services\OrderTotalService;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'status', 'total_net', 'total_gross'];
    protected $casts = ['status' => OrderStatus::class];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function setItems(array $items, OrderTotalService $svc): void
    {
        $productIds = collect($items)->pluck('product_id')->unique();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $rows = collect($items)->map(function ($row) use ($products)
        {
            $product = $products->get($row['product_id']);

            return [
                'product_id' => $product->id,
                'qty'        => $row['qty'],
                'price'      => $product->price,
                'vat_rate'   => $product->vat_rate,
            ];
        })->all();

        $this->items()->delete();
        $this->items()->createMany($rows);

        $totals = $svc->calculateTotals($this);
        $this->update(['total_net'=> Arr::get($totals, 'total_net'), 'total_gross'=> Arr::get($totals, 'total_gross')]);
    }
}
