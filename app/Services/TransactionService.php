<?php

namespace App\Services;

use App\Events\TransactionCreated;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionService
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected AuditLogService $auditLogService,
    ) {
    }

    public function createSale(User $user, array $payload): Transaction
    {
        // Seluruh proses penjualan dibungkus DB transaction supaya aman.
        // Kalau ada 1 langkah gagal, semua perubahan dibatalkan.
        return DB::transaction(function () use ($user, $payload) {
            $warehouse = Warehouse::query()->findOrFail($payload['warehouse_id']);
            $items = collect($payload['items']);

            // Hitung subtotal dari semua item yang dibeli.
            $subtotal = $items->sum(function (array $item) {
                $product = Product::query()->findOrFail($item['product_id']);
                return $product->price * $item['quantity'];
            });

            $discount = (float) Arr::get($payload, 'discount_amount', 0);
            $tax = (float) Arr::get($payload, 'tax_amount', 0);

            // Header transaksi utama disimpan dulu sebelum detail item.
            $transaction = Transaction::create([
                'company_id' => $user->company_id,
                'user_id' => $user->id,
                'warehouse_id' => $warehouse->id,
                'invoice_number' => 'INV-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4)),
                'customer_name' => Arr::get($payload, 'customer_name'),
                'customer_email' => Arr::get($payload, 'customer_email'),
                'status' => 'completed',
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'discount_amount' => $discount,
                'total_amount' => $subtotal + $tax - $discount,
                'notes' => Arr::get($payload, 'notes'),
                'transacted_at' => now(),
            ]);

            foreach ($items as $item) {
                $product = Product::query()->findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];

                // Pastikan stok cukup sebelum transaksi benar-benar dilanjutkan.
                $this->inventoryService->ensureStock($product, $warehouse, $quantity);

                // Simpan item transaksi satu per satu agar bisa dilacak detailnya.
                $transaction->details()->create([
                    'company_id' => $user->company_id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'line_total' => $product->price * $quantity,
                ]);

                // Setelah detail dibuat, stok langsung dikurangi.
                $this->inventoryService->adjustStock(
                    $product,
                    $warehouse,
                    -1 * $quantity,
                    'sale',
                    $user,
                    ['type' => Transaction::class, 'id' => $transaction->id],
                    'Stock reduced by POS transaction'
                );
            }

            // Audit log berguna untuk tahu siapa melakukan transaksi ini.
            $this->auditLogService->record('transaction.created', $transaction, [
                'invoice_number' => $transaction->invoice_number,
                'items' => $items->count(),
            ]);

            // Event realtime untuk notifikasi / dashboard.
            broadcast(new TransactionCreated($transaction->load('details.product', 'warehouse')))->toOthers();

            return $transaction->load('details.product', 'warehouse', 'user');
        });
    }
}
