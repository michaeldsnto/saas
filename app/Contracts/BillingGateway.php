<?php

namespace App\Contracts;

use App\Models\Company;
use App\Models\Plan;

interface BillingGateway
{
    public function createCheckout(Company $company, Plan $plan): array;
}
