<?php

namespace App\Http\Controllers;

use App\Exports\InventoryReportExport;
use App\Http\Requests\ImportProductsRequest;
use App\Http\Requests\ProductRequest;
use App\Imports\ProductsImport;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\AuditLogService;
use App\Services\InventoryService;
use App\Services\Prediction\StockPredictionService;
use App\Services\SubscriptionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index(StockPredictionService $predictionService, SubscriptionService $subscriptionService): View
    {
        $company = auth()->user()->company;
        $products = Product::query()->with(['category', 'supplier', 'stocks.warehouse'])->latest()->paginate(10);
        $canUsePrediction = $subscriptionService->hasFeature($company, 'prediction');
        $canUseImportExport = $subscriptionService->hasFeature($company, 'imports_exports');
        $predictions = $canUsePrediction
            ? $products->getCollection()->mapWithKeys(fn ($product) => [$product->id => $predictionService->predictRunOut($product)])
            : collect();

        return view('inventory.products', [
            'products' => $products,
            'categories' => Category::query()->get(),
            'suppliers' => Supplier::query()->get(),
            'warehouses' => Warehouse::query()->get(),
            'predictions' => $predictions,
            'canUsePrediction' => $canUsePrediction,
            'canUseImportExport' => $canUseImportExport,
            'currentPlan' => $subscriptionService->currentPlan($company),
        ]);
    }

    public function store(ProductRequest $request, SubscriptionService $subscriptionService, InventoryService $inventoryService, AuditLogService $auditLogService): RedirectResponse
    {
        abort_unless($subscriptionService->withinLimit($request->user()->company, 'products', Product::query()->count()), 422, 'Your plan product limit has been reached.');

        $payload = $request->validated();
        $product = Product::create($payload);

        if ($request->filled('warehouse_id') && $request->integer('opening_stock') > 0) {
            $inventoryService->adjustStock(
                $product,
                Warehouse::query()->findOrFail($request->integer('warehouse_id')),
                $request->integer('opening_stock'),
                'opening_balance',
                $request->user(),
                ['type' => Product::class, 'id' => $product->id],
                'Opening stock from product setup'
            );
        }

        $auditLogService->record('product.created', $product, $payload, $request);

        return back()->with('status', 'Product created.');
    }

    public function update(ProductRequest $request, Product $product, AuditLogService $auditLogService): RedirectResponse
    {
        $product->update($request->validated());
        $auditLogService->record('product.updated', $product, $request->validated(), $request);
        return back()->with('status', 'Product updated.');
    }

    public function destroy(Product $product, AuditLogService $auditLogService): RedirectResponse
    {
        $auditLogService->record('product.deleted', $product, [], request());
        $product->delete();
        return back()->with('status', 'Product deleted.');
    }

    public function import(ImportProductsRequest $request, SubscriptionService $subscriptionService): RedirectResponse
    {
        abort_unless($subscriptionService->hasFeature($request->user()->company, 'imports_exports'), 403, 'Import is available on Pro and Enterprise plans.');

        Excel::import(new ProductsImport($request->user(), $request->integer('warehouse_id')), $request->file('file'));
        return back()->with('status', 'Products imported successfully.');
    }

    public function exportExcel(SubscriptionService $subscriptionService)
    {
        abort_unless($subscriptionService->hasFeature(auth()->user()->company, 'imports_exports'), 403, 'Export is available on Pro and Enterprise plans.');

        return Excel::download(new InventoryReportExport, 'inventory-report.xlsx');
    }

    public function exportPdf(SubscriptionService $subscriptionService)
    {
        abort_unless($subscriptionService->hasFeature(auth()->user()->company, 'imports_exports'), 403, 'Export is available on Pro and Enterprise plans.');

        $products = Product::query()->with(['category', 'supplier', 'stocks.warehouse'])->get();
        return Pdf::loadView('reports.inventory-pdf', compact('products'))->download('inventory-report.pdf');
    }
}
