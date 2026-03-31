<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\Prediction\StockPredictionService;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function __construct(protected StockPredictionService $predictionService)
    {
    }

    public function dashboard(Company $company): array
    {
        $revenue = Transaction::query()->sum('total_amount');
        $salesTrend = Transaction::query()
            ->selectRaw('DATE(transacted_at) as day, SUM(total_amount) as revenue')
            ->groupBy('day')
            ->orderBy('day')
            ->limit(14)
            ->get();

        $topProducts = Product::query()
            ->withSum('transactionDetails as sold_quantity', 'quantity')
            ->orderByDesc('sold_quantity')
            ->limit(5)
            ->get();

        $stockUsage = DB::table('transaction_details')
            ->selectRaw('SUM(quantity) as quantity_used')
            ->where('company_id', $company->id)
            ->value('quantity_used') ?? 0;

        $insights = [];
        foreach ($topProducts->take(3) as $product) {
            $prediction = $this->predictionService->predictRunOut($product);
            if ($prediction) {
                $insights[] = sprintf('%s may run out around %s based on recent sales.', $product->name, $prediction['estimated_run_out_date']);
            }
        }

        return [
            'revenue' => $revenue,
            'sales_trend' => $salesTrend,
            'top_products' => $topProducts,
            'stock_usage' => $stockUsage,
            'insights' => $insights,
        ];
    }
}
