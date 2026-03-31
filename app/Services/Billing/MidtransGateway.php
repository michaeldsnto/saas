<?php

namespace App\Services\Billing;

use App\Contracts\BillingGateway;
use App\Models\Company;
use App\Models\Plan;

class MidtransGateway implements BillingGateway
{
    public function createCheckout(Company $company, Plan $plan): array
    {
        return [
            'provider' => 'midtrans',
            'checkout_url' => '#',
            'reference' => 'MID-' . $company->id . '-' . now()->timestamp,
            'message' => 'Replace this stub with a real Midtrans Snap transaction request in production.',
        ];
    }
}
