<?php

namespace App\Support\Tenant;

use App\Models\Company;

class TenantManager
{
    protected ?int $companyId = null;

    protected ?Company $company = null;

    public function set(?Company $company): void
    {
        $this->company = $company;
        $this->companyId = $company?->getKey();
    }

    public function company(): ?Company
    {
        return $this->company;
    }

    public function id(): ?int
    {
        return $this->companyId;
    }

    public function hasTenant(): bool
    {
        return $this->companyId !== null;
    }

    public function clear(): void
    {
        $this->companyId = null;
        $this->company = null;
    }
}
