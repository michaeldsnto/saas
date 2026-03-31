<?php

namespace App\Services\Prediction;

use App\Models\Product;
use Carbon\Carbon;

class StockPredictionService
{
    public function predictRunOut(Product $product): ?array
    {
        $dailyUsage = $product->transactionDetails()
            ->selectRaw('DATE(created_at) as day, SUM(quantity) as total_quantity')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('day')
            ->pluck('total_quantity');

        if ($dailyUsage->isEmpty()) {
            return null;
        }

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
