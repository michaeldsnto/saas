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
        // Kalau kombinasi product + warehouse belum punya row stock,
        // kita buat dulu dengan quantity 0 supaya query berikutnya konsisten.
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
        // quantity bisa positif atau negatif:
        // +10 = restock / opening stock
        // -3  = stok berkurang karena penjualan, retur keluar, dll
        $stock = Stock::query()->firstOrCreate([
            'company_id' => $product->company_id,
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
        ], [
            'quantity' => 0,
        ]);

        $stock->increment('quantity', $quantity);
        $stock->refresh();

        // Selain ubah stok utama, kita simpan jejak pergerakannya.
        // Ini membantu audit dan histori stok di dunia nyata.
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

        // Event ini dipakai untuk kebutuhan realtime, misalnya dashboard
        // atau halaman stok yang ingin ikut berubah tanpa refresh manual.
        broadcast(new StockUpdated($stock))->toOthers();

        // Kalau stok sudah menyentuh batas minimum, kirim alert khusus.
        if ($stock->quantity <= $product->minimum_stock) {
            broadcast(new LowStockDetected($stock))->toOthers();
        }

        return $stock;
    }

    public function lowStockProducts()
    {
        // Mengambil produk yang total stoknya sudah di bawah atau sama
        // dengan batas minimum yang ditentukan.
        return Product::query()->with('stocks')->get()->filter(fn (Product $product) => $product->totalStock() <= $product->minimum_stock);
    }
}
