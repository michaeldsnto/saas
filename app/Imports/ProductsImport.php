<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function __construct(protected User $user, protected ?int $warehouseId = null)
    {
    }

    public function model(array $row)
    {
        $category = null;

        if (! empty($row['category'])) {
            $category = Category::firstOrCreate([
                'company_id' => $this->user->company_id,
                'name' => $row['category'],
            ]);
        }

        $product = Product::updateOrCreate([
            'company_id' => $this->user->company_id,
            'sku' => $row['sku'] ?: Str::upper(Str::random(8)),
        ], [
            'category_id' => $category?->id,
            'name' => $row['name'],
            'price' => $row['price'] ?? 0,
            'cost_price' => $row['cost_price'] ?? 0,
            'minimum_stock' => $row['minimum_stock'] ?? 0,
            'description' => $row['description'] ?? null,
            'is_active' => true,
        ]);

        if ($this->warehouseId && isset($row['stock'])) {
            Stock::updateOrCreate([
                'company_id' => $this->user->company_id,
                'product_id' => $product->id,
                'warehouse_id' => $this->warehouseId,
            ], [
                'quantity' => (int) $row['stock'],
            ]);
        }

        return $product;
    }
}
