<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyUpdateRequest;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompanySettingsController extends Controller
{
    public function edit(): View
    {
        return view('company.settings', ['company' => auth()->user()->company]);
    }

    public function update(CompanyUpdateRequest $request, AuditLogService $auditLogService): RedirectResponse
    {
        $company = $request->user()->company;
        $company->update($request->validated());

        $auditLogService->record('company.updated', $company, $request->validated(), $request);

        return back()->with('status', 'Company updated successfully.');
    }
}
