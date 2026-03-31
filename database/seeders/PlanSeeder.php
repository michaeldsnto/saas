<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Free', 'slug' => 'free', 'price' => 0, 'billing_cycle' => 'monthly', 'product_limit' => 25, 'warehouse_limit' => 1, 'staff_limit' => 2, 'features' => ['dashboard', 'inventory', 'basic_analytics']],
            ['name' => 'Pro', 'slug' => 'pro', 'price' => 299000, 'billing_cycle' => 'monthly', 'product_limit' => 500, 'warehouse_limit' => 5, 'staff_limit' => 15, 'features' => ['dashboard', 'inventory', 'analytics', 'api', 'prediction']],
            ['name' => 'Enterprise', 'slug' => 'enterprise', 'price' => 999000, 'billing_cycle' => 'monthly', 'product_limit' => 10000, 'warehouse_limit' => 50, 'staff_limit' => 100, 'features' => ['dashboard', 'inventory', 'analytics', 'api', 'prediction', 'priority_support']],
        ] as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan + ['is_active' => true]);
        }
    }
}
