<?php

namespace App\Http\Controllers\Store;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderTotalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * @group Order
     * @authenticated
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('items.product')->latest()->paginate(10);

        return response()->json(OrderResource::collection($orders));
    }

    /**
     * @group Order
     * @authenticated
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request, OrderTotalService $orderTotalService)
    {
        try
        {
            $order = DB::transaction(function () use ($request, $orderTotalService) {
                /** @var Order $order */
                $order = Order::create([
                    'user_id' => $request->user()->id,
                    'status'  => OrderStatus::Pending,
                ]);

                $order->setItems($request->items, $orderTotalService);
                return $order;
            });

            return new OrderResource($order->load('items.product'));
        }
        catch (\Exception $exception)
        {
            Log::error($exception->getMessage());
            return response()->json(['message' => __('Something went wrong')],500);
        }
    }

    /**
     * @group Order
     * @authenticated
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * @group Order
     * @authenticated
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * @group Order
     * @authenticated
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
