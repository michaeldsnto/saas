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
        static::creating(function (Model $model): void {
            $tenantId = app(TenantManager::class)->id();

            if ($tenantId && empty($model->company_id)) {
                $model->company_id = $tenantId;
            }
        });

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
        return $query->withoutGlobalScope('company')->where('company_id', $companyId);
    }
}
