<?php

namespace App\Console\Commands;

use App\Services\InventoryService;
use Illuminate\Console\Command;

class SendLowStockAlerts extends Command
{
    protected $signature = 'inventory:low-stock-alerts';

    protected $description = 'Scan current inventory and list low stock products.';

    public function handle(InventoryService $inventoryService): int
    {
        $products = $inventoryService->lowStockProducts();

        foreach ($products as $product) {
            $this->line($product->name . ' => stock ' . $product->totalStock());
        }

        return self::SUCCESS;
    }
}
