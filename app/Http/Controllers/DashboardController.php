<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\InventoryService;
use App\Services\Prediction\StockPredictionService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(AnalyticsService $analyticsService, InventoryService $inventoryService, StockPredictionService $predictionService): View
    {
        $company = auth()->user()->company;
        $analytics = $analyticsService->dashboard($company);
        $lowStocks = $inventoryService->lowStockProducts();
        $predictions = $lowStocks->take(5)->mapWithKeys(fn ($product) => [$product->id => $predictionService->predictRunOut($product)]);

        return view('dashboard', compact('company', 'analytics', 'lowStocks', 'predictions'));
    }
}
