<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * @group Product
     * @authenticated
     */
    public function index()
    {
        $products = Cache::tags(['products'])
            ->remember('list', 300, fn()=>Product::query()->available()->paginate(15));

        return response()->json([
            'data' => ProductResource::collection($products),
            'meta' => ['columns'=> ProductResource::COLUMNS],
        ]);
    }

    /**
     * @group Product
     * @authenticated
     */
    public function store(ProductRequest $request)
    {
        try
        {
            $product = Product::create($request->validated());

            Cache::tags(['products'])->flush();

            return response()->json(new ProductResource($product),201);
        }
        catch (\Exception $exception)
        {
            Log::error($exception->getMessage());
            return response()->json(['message' => __('Something went wrong')],500);
        }
    }

    /**
     * @group Product
     * @authenticated
     */
    public function show(string $id)
    {
        //
    }

    /**
     * @group Product
     * @authenticated
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * @group Product
     * @authenticated
     */
    public function destroy(string $id)
    {
        //
    }
}
