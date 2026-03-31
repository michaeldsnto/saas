<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\Billing\PaymentGatewayFactory;

class SubscriptionService
{
    public function __construct(protected PaymentGatewayFactory $gatewayFactory)
    {
    }

    public function activeSubscription(Company $company): ?Subscription
    {
        // Mengambil subscription yang masih dianggap aktif untuk company ini.
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

    public function hasFeature(Company $company, string $feature): bool
    {
        $plan = $this->currentPlan($company);

        if (! $plan) {
            return false;
        }

        $features = collect($plan->features ?? []);

        return match ($feature) {
            'prediction' => $features->contains('prediction'),
            'advanced_analytics' => $features->contains('analytics'),
            'api' => $features->contains('api'),
            'imports_exports' => $plan->slug !== 'free',
            default => $features->contains($feature),
        };
    }

    public function usageSummary(Company $company): array
    {
        $plan = $this->currentPlan($company);

        if (! $plan) {
            return [];
        }

        $productsUsed = Product::query()->withoutGlobalScopes()->where('company_id', $company->id)->count();
        $warehousesUsed = Warehouse::query()->withoutGlobalScopes()->where('company_id', $company->id)->count();
        $staffUsed = User::query()->withoutGlobalScopes()->where('company_id', $company->id)->whereHas('role', fn ($query) => $query->where('slug', 'staff'))->count();

        return [
            'products' => $this->makeUsageItem($productsUsed, $plan->product_limit, 'Products'),
            'warehouses' => $this->makeUsageItem($warehousesUsed, $plan->warehouse_limit, 'Warehouses'),
            'staff' => $this->makeUsageItem($staffUsed, $plan->staff_limit, 'Staff'),
        ];
    }

    public function usageAlerts(Company $company): array
    {
        return collect($this->usageSummary($company))
            ->filter(fn (array $item) => in_array($item['status'], ['warning', 'limit-reached'], true))
            ->values()
            ->all();
    }

    protected function makeUsageItem(int $used, int $limit, string $label): array
    {
        $percentage = $limit > 0 ? (int) min(100, round(($used / $limit) * 100)) : 0;
        $remaining = max(0, $limit - $used);

        return [
            'label' => $label,
            'used' => $used,
            'limit' => $limit,
            'remaining' => $remaining,
            'percentage' => $percentage,
            'status' => $used >= $limit ? 'limit-reached' : ($percentage >= 80 ? 'warning' : 'healthy'),
        ];
    }

    public function withinLimit(Company $company, string $resource, int $currentCount): bool
    {
        // Method ini dipakai sebelum create data tertentu.
        // Tujuannya agar fitur mengikuti batas plan yang dipilih company.
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
        // Factory memilih adapter gateway yang sesuai, misalnya Midtrans atau Xendit.
        $checkout = $this->gatewayFactory->make($gateway)->createCheckout($company, $plan);

        // Di versi sekarang, checkout masih bersifat simulasi / stub.
        // Tapi struktur datanya sudah disiapkan agar nanti mudah diganti
        // ke integrasi gateway sungguhan.
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

        // Payment disimpan terpisah supaya histori billing lebih jelas.
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
