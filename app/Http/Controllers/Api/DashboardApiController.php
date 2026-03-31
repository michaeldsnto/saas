<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    public function __invoke(AnalyticsService $analyticsService): JsonResponse
    {
        return response()->json(['data' => $analyticsService->dashboard(auth()->user()->company)]);
    }
}
