<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseRequest;
use App\Models\Warehouse;
use App\Services\AuditLogService;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function index(): View
    {
        return view('inventory.warehouses', ['warehouses' => Warehouse::query()->latest()->get()]);
    }

    public function store(WarehouseRequest $request, SubscriptionService $subscriptionService, AuditLogService $auditLogService): RedirectResponse
    {
        abort_unless($subscriptionService->withinLimit($request->user()->company, 'warehouses', Warehouse::query()->count()), 422, 'Your plan warehouse limit has been reached.');

        if ($request->boolean('is_default')) {
            Warehouse::query()->update(['is_default' => false]);
        }

        $warehouse = Warehouse::create($request->validated());
        $auditLogService->record('warehouse.created', $warehouse, $request->validated(), $request);

        return back()->with('status', 'Warehouse created.');
    }

    public function update(WarehouseRequest $request, Warehouse $warehouse, AuditLogService $auditLogService): RedirectResponse
    {
        if ($request->boolean('is_default')) {
            Warehouse::query()->whereKeyNot($warehouse->id)->update(['is_default' => false]);
        }

        $warehouse->update($request->validated());
        $auditLogService->record('warehouse.updated', $warehouse, $request->validated(), $request);

        return back()->with('status', 'Warehouse updated.');
    }

    public function destroy(Warehouse $warehouse, AuditLogService $auditLogService): RedirectResponse
    {
        $auditLogService->record('warehouse.deleted', $warehouse, [], request());
        $warehouse->delete();
        return back()->with('status', 'Warehouse deleted.');
    }
}
