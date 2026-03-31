<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventoryReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Product::query()->with(['category', 'supplier', 'stocks'])->get();
    }

    public function headings(): array
    {
        return ['SKU', 'Product', 'Category', 'Supplier', 'Price', 'Minimum Stock', 'Current Stock'];
    }

    public function map($product): array
    {
        return [
            $product->sku,
            $product->name,
            $product->category?->name,
            $product->supplier?->name,
            $product->price,
            $product->minimum_stock,
            $product->stocks->sum('quantity'),
        ];
    }
}
