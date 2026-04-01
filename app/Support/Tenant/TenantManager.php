<?php

namespace App\Support\Tenant;

use App\Models\Company;

class TenantManager
{
    // Kelas kecil ini menyimpan "company yang sedang aktif" selama 1 request.
    // Model, middleware, dan service lain tinggal membaca dari sini.
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
