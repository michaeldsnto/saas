<?php

namespace App\Services;

use App\Events\LowStockDetected;
use App\Events\StockUpdated;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;

class InventoryService
{
    public function ensureStock(Product $product, Warehouse $warehouse, int $quantity): void
    {
        $stock = Stock::query()->firstOrCreate([
            'company_id' => $product->company_id,
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
        ], [
            'quantity' => 0,
        ]);

        if ($stock->quantity < $quantity) {
            abort(422, "Insufficient stock for {$product->name} in {$warehouse->name}.");
        }
    }

    public function adjustStock(Product $product, Warehouse $warehouse, int $quantity, string $type, ?User $user = null, array $reference = [], ?string $notes = null): Stock
    {
        $stock = Stock::query()->firstOrCreate([
            'company_id' => $product->company_id,
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
        ], [
            'quantity' => 0,
        ]);

        $stock->increment('quantity', $quantity);
        $stock->refresh();

        StockMovement::create([
            'company_id' => $product->company_id,
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'user_id' => $user?->id,
            'type' => $type,
            'quantity' => $quantity,
            'reference_type' => $reference['type'] ?? null,
            'reference_id' => $reference['id'] ?? null,
            'notes' => $notes,
        ]);

        broadcast(new StockUpdated($stock))->toOthers();

        if ($stock->quantity <= $product->minimum_stock) {
            broadcast(new LowStockDetected($stock))->toOthers();
        }

        return $stock;
    }

    public function lowStockProducts()
    {
        return Product::query()->with('stocks')->get()->filter(fn (Product $product) => $product->totalStock() <= $product->minimum_stock);
    }
}
