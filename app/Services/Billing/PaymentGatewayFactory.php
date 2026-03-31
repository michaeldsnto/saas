<?php

namespace App\Services\Billing;

use App\Contracts\BillingGateway;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    public function make(string $gateway): BillingGateway
    {
        return match ($gateway) {
            'midtrans' => app(MidtransGateway::class),
            'xendit' => app(XenditGateway::class),
            default => throw new InvalidArgumentException("Unsupported gateway [{$gateway}]."),
        };
    }
}
