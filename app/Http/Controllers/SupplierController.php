<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        return view('inventory.suppliers', ['suppliers' => Supplier::query()->latest()->get()]);
    }

    public function store(SupplierRequest $request, AuditLogService $auditLogService): RedirectResponse
    {
        $supplier = Supplier::create($request->validated());
        $auditLogService->record('supplier.created', $supplier, $request->validated(), $request);
        return back()->with('status', 'Supplier created.');
    }

    public function update(SupplierRequest $request, Supplier $supplier, AuditLogService $auditLogService): RedirectResponse
    {
        $supplier->update($request->validated());
        $auditLogService->record('supplier.updated', $supplier, $request->validated(), $request);
        return back()->with('status', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier, AuditLogService $auditLogService): RedirectResponse
    {
        $auditLogService->record('supplier.deleted', $supplier, [], request());
        $supplier->delete();
        return back()->with('status', 'Supplier deleted.');
    }
}
