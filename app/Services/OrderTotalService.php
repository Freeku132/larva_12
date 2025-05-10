<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class OrderTotalService
{
    /**
     * @param  Order  $order
     * @return array{net: float, gross: float}
     */
    public function calculateTotals(Order $order): array
    {
        $cacheKey = "order_totals:{$order->id}";

        return Cache::tags(['orders'])
            ->remember($cacheKey, now()->addMinutes(10), function () use ($order) {
                $net   = 0.0;
                $gross = 0.0;

                foreach ($order->items as $item) {
                    $rowNet   = $item->price * $item->qty;
                    $rowGross = $rowNet * (1 + $item->vat_rate / 100);

                    $net   += $rowNet;
                    $gross += $rowGross;
                }

                return [
                    'total_net'   => $net,
                    'total_gross' => $gross,
                ];
            });
    }

    /**
     * @param  Order  $order
     * @return void
     */
    public function clearCache(Order $order): void
    {
        Cache::tags(['orders'])->forget("order_totals:{$order->id}");
    }
}
