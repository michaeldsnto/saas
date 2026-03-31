<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Role;
use App\Models\Stock;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_transaction_reduces_stock(): void
    {
        $this->seed();

        $company = Company::create(['name' => 'Store', 'slug' => 'store-' . Str::lower(Str::random(4)), 'timezone' => 'Asia/Jakarta', 'currency' => 'IDR']);
        $role = Role::where('slug', Role::OWNER)->firstOrFail();
        $plan = Plan::where('slug', 'free')->firstOrFail();

        Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'gateway' => 'midtrans',
            'gateway_reference' => Str::uuid()->toString(),
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        $user = User::create(['company_id' => $company->id, 'role_id' => $role->id, 'name' => 'Owner', 'email' => 'owner@store.test', 'password' => 'password', 'is_active' => true, 'email_verified_at' => now()]);
        $warehouse = Warehouse::create(['company_id' => $company->id, 'name' => 'Main', 'code' => 'MAIN', 'is_default' => true]);
        $product = Product::create(['company_id' => $company->id, 'sku' => 'SKU-1', 'name' => 'Product', 'price' => 10000, 'cost_price' => 7000, 'minimum_stock' => 2, 'is_active' => true]);
        Stock::create(['company_id' => $company->id, 'product_id' => $product->id, 'warehouse_id' => $warehouse->id, 'quantity' => 10]);

        $this->actingAs($user)
            ->post(route('transactions.store'), [
                'warehouse_id' => $warehouse->id,
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 3],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 7,
        ]);
    }
}
