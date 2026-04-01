<?php

namespace App\Models\Concerns;

use App\Models\Company;
use App\Support\Tenant\TenantManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        // Saat record baru dibuat, company_id akan otomatis diisi dari tenant
        // yang sedang aktif. Ini mencegah kita lupa mengisi company_id manual.
        static::creating(function (Model $model): void {
            $tenantId = app(TenantManager::class)->id();

            if ($tenantId && empty($model->company_id)) {
                $model->company_id = $tenantId;
            }
        });

        // Global scope ini penting untuk multi-tenant.
        // Selama ada tenant aktif, query model hanya akan membaca data
        // milik company tersebut.
        static::addGlobalScope('company', function (Builder $builder): void {
            $tenantId = app(TenantManager::class)->id();

            if ($tenantId) {
                $builder->where($builder->qualifyColumn('company_id'), $tenantId);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        // Scope ini berguna kalau kita memang sengaja ingin memilih company
        // tertentu tanpa memakai tenant aktif dari request sekarang.
        return $query->withoutGlobalScope('company')->where('company_id', $companyId);
    }
}
