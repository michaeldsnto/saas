<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditLogService
{
    public function record(string $action, ?Model $model = null, array $metadata = [], ?Request $request = null): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        AuditLog::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'action' => $action,
            'auditable_type' => $model ? $model::class : null,
            'auditable_id' => $model?->getKey(),
            'metadata' => $metadata,
            'ip_address' => $request?->ip(),
        ]);
    }
}
