<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionStoreRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;

class TransactionApiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Transaction::query()->with(['details.product', 'warehouse', 'user'])->paginate());
    }

    public function store(TransactionStoreRequest $request, TransactionService $transactionService): JsonResponse
    {
        return response()->json(['data' => $transactionService->createSale($request->user(), $request->validated())], 201);
    }
}
