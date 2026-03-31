<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionStoreRequest;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Warehouse;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(): View
    {
        return view('transactions.index', [
            'transactions' => Transaction::query()->with(['details.product', 'warehouse', 'user'])->latest()->paginate(10),
            'products' => Product::query()->with('stocks')->get(),
            'warehouses' => Warehouse::query()->get(),
        ]);
    }

    public function store(TransactionStoreRequest $request, TransactionService $transactionService): RedirectResponse|JsonResponse
    {
        $transaction = $transactionService->createSale($request->user(), $request->validated());

        if ($request->wantsJson()) {
            return response()->json(['data' => $transaction]);
        }

        return back()->with('status', 'Transaction created: ' . $transaction->invoice_number);
    }

    public function show(Transaction $transaction): View
    {
        return view('transactions.show', ['transaction' => $transaction->load(['details.product', 'warehouse', 'user'])]);
    }
}
