<?php

namespace App\Services\Prediction;

use App\Models\Product;
use Carbon\Carbon;

class StockPredictionService
{
    public function predictRunOut(Product $product): ?array
    {
        // Kita ambil pemakaian stok per hari dalam 30 hari terakhir.
        // Data ini dipakai untuk pendekatan moving average sederhana.
        $dailyUsage = $product->transactionDetails()
            ->selectRaw('DATE(created_at) as day, SUM(quantity) as total_quantity')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('day')
            ->pluck('total_quantity');

        // Kalau belum ada histori transaksi, prediksi belum bisa dibuat.
        if ($dailyUsage->isEmpty()) {
            return null;
        }

        // average_daily_usage = rata-rata barang keluar per hari
        // daysRemaining      = kira-kira stok bertahan berapa hari lagi
        $averageUsage = max(1, (float) round($dailyUsage->avg(), 2));
        $remainingStock = max(0, $product->totalStock());
        $daysRemaining = (int) ceil($remainingStock / $averageUsage);

        return [
            'average_daily_usage' => $averageUsage,
            'remaining_stock' => $remainingStock,
            'estimated_run_out_date' => Carbon::now()->addDays($daysRemaining)->toDateString(),
            'recommended_restock_quantity' => (int) ceil($averageUsage * 14),
        ];
    }
}
