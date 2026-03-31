<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\InventoryService;
use App\Services\Prediction\StockPredictionService;
use App\Services\SubscriptionService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(
        AnalyticsService $analyticsService,
        InventoryService $inventoryService,
        StockPredictionService $predictionService,
        SubscriptionService $subscriptionService,
    ): View {
        $company = auth()->user()->company;
        $analytics = $analyticsService->dashboard($company);
        $lowStocks = $inventoryService->lowStockProducts();
        $canUsePrediction = $subscriptionService->hasFeature($company, 'prediction');
        $canUseAdvancedAnalytics = $subscriptionService->hasFeature($company, 'advanced_analytics');
        $predictions = $canUsePrediction
            ? $lowStocks->take(5)->mapWithKeys(fn ($product) => [$product->id => $predictionService->predictRunOut($product)])
            : collect();
        $subscription = $subscriptionService->activeSubscription($company);
        $usage = $subscriptionService->usageSummary($company);

        return view('dashboard', compact('company', 'analytics', 'lowStocks', 'predictions', 'subscription', 'usage', 'canUsePrediction', 'canUseAdvancedAnalytics'));
    }
}
