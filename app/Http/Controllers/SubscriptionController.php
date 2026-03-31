<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionCheckoutRequest;
use App\Models\Payment;
use App\Models\Plan;
use App\Services\AuditLogService;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(SubscriptionService $subscriptionService): View
    {
        $company = auth()->user()->company;

        return view('billing.index', [
            'plans' => Plan::query()->where('is_active', true)->get(),
            'subscription' => $subscriptionService->activeSubscription($company),
            'payments' => Payment::query()->latest()->get(),
        ]);
    }

    public function checkout(SubscriptionCheckoutRequest $request, SubscriptionService $subscriptionService, AuditLogService $auditLogService): RedirectResponse
    {
        $plan = Plan::query()->findOrFail($request->integer('plan_id'));
        [$subscription, $checkout] = $subscriptionService->checkout($request->user()->company, $plan, $request->string('gateway'));

        $auditLogService->record('subscription.checkout', $subscription, $checkout, $request);

        return back()->with('status', 'Subscription created. Gateway reference: ' . $checkout['reference']);
    }
}
