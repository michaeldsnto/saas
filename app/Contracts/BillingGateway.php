<?php

namespace App\Contracts;

use App\Models\Company;
use App\Models\Plan;

interface BillingGateway
{
    // Contract ini sengaja dibuat sederhana:
    // gateway apa pun cukup bisa membuat data checkout dari company + plan.
    // Nanti implementasinya bisa Midtrans, Xendit, atau provider lain.
    public function createCheckout(Company $company, Plan $plan): array;
}
