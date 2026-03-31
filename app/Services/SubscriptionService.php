<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\Billing\PaymentGatewayFactory;

class SubscriptionService
{
    public function __construct(protected PaymentGatewayFactory $gatewayFactory)
    {
    }

    public function activeSubscription(Company $company): ?Subscription
    {
        return Subscription::query()
            ->where('company_id', $company->id)
            ->whereIn('status', ['active', 'trial'])
            ->latest('ends_at')
            ->first();
    }

    public function currentPlan(Company $company): ?Plan
    {
        return $this->activeSubscription($company)?->plan;
    }

    public function withinLimit(Company $company, string $resource, int $currentCount): bool
    {
        $plan = $this->currentPlan($company);

        if (! $plan) {
            return false;
        }

        return match ($resource) {
            'products' => $currentCount < $plan->product_limit,
            'warehouses' => $currentCount < $plan->warehouse_limit,
            'staff' => $currentCount < $plan->staff_limit,
            default => true,
        };
    }

    public function checkout(Company $company, Plan $plan, string $gateway): array
    {
        $checkout = $this->gatewayFactory->make($gateway)->createCheckout($company, $plan);

        $subscription = Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'gateway' => $gateway,
            'gateway_reference' => $checkout['reference'],
            'status' => $plan->price > 0 ? 'pending' : 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'trial_ends_at' => $plan->price == 0 ? now()->addDays(14) : null,
            'meta' => $checkout,
        ]);

        Payment::create([
            'company_id' => $company->id,
            'subscription_id' => $subscription->id,
            'provider' => $gateway,
            'provider_reference' => $checkout['reference'],
            'amount' => $plan->price,
            'currency' => $company->currency,
            'status' => $plan->price > 0 ? 'pending' : 'paid',
            'paid_at' => $plan->price == 0 ? now() : null,
            'payload' => $checkout,
        ]);

        return [$subscription, $checkout];
    }
}
