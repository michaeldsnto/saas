<?php

namespace App\Services\Billing;

use App\Contracts\BillingGateway;
use App\Models\Company;
use App\Models\Plan;

class XenditGateway implements BillingGateway
{
    public function createCheckout(Company $company, Plan $plan): array
    {
        return [
            'provider' => 'xendit',
            'checkout_url' => '#',
            'reference' => 'XEN-' . $company->id . '-' . now()->timestamp,
            'message' => 'Replace this stub with a real Xendit invoice request in production.',
        ];
    }
}
