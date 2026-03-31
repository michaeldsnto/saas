<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;

class ProductApiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Product::query()->with(['category', 'supplier', 'stocks'])->paginate());
    }

    public function store(ProductRequest $request, InventoryService $inventoryService): JsonResponse
    {
        $product = Product::create($request->validated());

        if ($request->filled('warehouse_id') && $request->integer('opening_stock') > 0) {
            $inventoryService->adjustStock(
                $product,
                Warehouse::query()->findOrFail($request->integer('warehouse_id')),
                $request->integer('opening_stock'),
                'opening_balance',
                $request->user()
            );
        }

        return response()->json(['data' => $product->load('stocks')], 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json(['data' => $product->load(['category', 'supplier', 'stocks.warehouse'])]);
    }

    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());
        return response()->json(['data' => $product->fresh()]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json(status: 204);
    }
}
