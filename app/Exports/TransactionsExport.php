<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Transaction::query()->with('user', 'warehouse')->latest()->get();
    }

    public function headings(): array
    {
        return ['Invoice', 'Date', 'Warehouse', 'Cashier', 'Customer', 'Total'];
    }

    public function map($transaction): array
    {
        return [
            $transaction->invoice_number,
            optional($transaction->transacted_at)->toDateTimeString(),
            $transaction->warehouse?->name,
            $transaction->user?->name,
            $transaction->customer_name,
            $transaction->total_amount,
        ];
    }
}
