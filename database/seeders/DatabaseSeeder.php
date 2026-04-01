<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Company;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Role;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Subscription;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([RoleSeeder::class, PlanSeeder::class]);

        $ownerRole = Role::where('slug', Role::OWNER)->firstOrFail();
        $staffRole = Role::where('slug', Role::STAFF)->firstOrFail();
        $proPlan = Plan::where('slug', 'pro')->firstOrFail();
        $enterprisePlan = Plan::where('slug', 'enterprise')->firstOrFail();

        $this->seedMainDemoCompany($ownerRole, $staffRole, $proPlan);
        $this->seedSecondaryDemoCompany($ownerRole, $staffRole, $enterprisePlan);
    }

    protected function seedMainDemoCompany(Role $ownerRole, Role $staffRole, Plan $plan): void
    {
        $company = Company::create([
            'name' => 'Nusantara Smart Retail',
            'slug' => 'nusantara-smart-retail',
            'email' => 'hello@nusantararetail.test',
            'phone' => '0812-1111-2222',
            'address' => 'Jl. Ahmad Yani No. 88',
            'city' => 'Makassar',
            'country' => 'Indonesia',
            'timezone' => 'Asia/Makassar',
            'currency' => 'IDR',
            'settings' => [
                'dark_mode' => true,
                'stock_alerts' => true,
            ],
        ]);

        $owner = User::create([
            'company_id' => $company->id,
            'role_id' => $ownerRole->id,
            'name' => 'Demo Owner',
            'email' => 'owner@example.com',
            'phone' => '0812-1111-2222',
            'job_title' => 'Founder',
            'password' => Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $staffA = User::create([
            'company_id' => $company->id,
            'role_id' => $staffRole->id,
            'name' => 'Ayu Kasir',
            'email' => 'ayu@example.com',
            'phone' => '0812-3333-4444',
            'job_title' => 'Cashier',
            'password' => Hash::make('password'),
            'is_active' => true,
            'invited_by' => $owner->id,
            'email_verified_at' => now(),
        ]);

        $staffB = User::create([
            'company_id' => $company->id,
            'role_id' => $staffRole->id,
            'name' => 'Rian Gudang',
            'email' => 'rian@example.com',
            'phone' => '0812-5555-6666',
            'job_title' => 'Warehouse Staff',
            'password' => Hash::make('password'),
            'is_active' => true,
            'invited_by' => $owner->id,
            'email_verified_at' => now(),
        ]);

        $subscription = Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'gateway' => 'midtrans',
            'gateway_reference' => 'MID-DEMO-001',
            'status' => 'active',
            'starts_at' => now()->subDays(20),
            'ends_at' => now()->addDays(10),
            'trial_ends_at' => null,
            'meta' => [
                'checkout_url' => '#',
                'message' => 'Demo seeded billing record',
            ],
        ]);

        Payment::create([
            'company_id' => $company->id,
            'subscription_id' => $subscription->id,
            'provider' => 'midtrans',
            'provider_reference' => 'PAY-DEMO-001',
            'amount' => $plan->price,
            'currency' => 'IDR',
            'status' => 'paid',
            'paid_at' => now()->subDays(20),
            'payload' => [
                'method' => 'bank_transfer',
                'note' => 'Dummy paid record for demo dashboard',
            ],
        ]);

        Payment::create([
            'company_id' => $company->id,
            'subscription_id' => $subscription->id,
            'provider' => 'midtrans',
            'provider_reference' => 'PAY-DEMO-002',
            'amount' => $plan->price,
            'currency' => 'IDR',
            'status' => 'pending',
            'paid_at' => null,
            'payload' => [
                'method' => 'virtual_account',
                'note' => 'Dummy pending invoice for review',
            ],
        ]);

        $categories = collect([
            'Beverages',
            'Snacks',
            'Instant Food',
            'Household',
        ])->mapWithKeys(fn (string $name) => [$name => Category::create([
            'company_id' => $company->id,
            'name' => $name,
            'description' => 'Demo category for seeded data',
        ])]);

        $suppliers = collect([
            'PT Sumber Makmur',
            'CV Laut Timur Distribusi',
            'PT Pangan Sentosa',
        ])->mapWithKeys(fn (string $name) => [$name => Supplier::create([
            'company_id' => $company->id,
            'name' => $name,
            'email' => Str::slug($name) . '@supplier.test',
            'phone' => '0411-700-' . fake()->numberBetween(100, 999),
            'contact_person' => fake()->name(),
            'address' => fake()->address(),
        ])]);

        $mainWarehouse = Warehouse::create([
            'company_id' => $company->id,
            'name' => 'Main Warehouse',
            'code' => 'MAIN',
            'address' => 'Makassar Distribution Center',
            'is_default' => true,
        ]);

        $storeWarehouse = Warehouse::create([
            'company_id' => $company->id,
            'name' => 'Front Store',
            'code' => 'STORE',
            'address' => 'Retail Outlet Floor',
            'is_default' => false,
        ]);

        $products = [
            ['sku' => 'BEV-001', 'name' => 'Air Mineral 600ml', 'category' => 'Beverages', 'supplier' => 'PT Sumber Makmur', 'price' => 5000, 'cost' => 3200, 'min' => 25, 'main' => 80, 'store' => 14],
            ['sku' => 'BEV-002', 'name' => 'Teh Botol Original', 'category' => 'Beverages', 'supplier' => 'PT Sumber Makmur', 'price' => 7000, 'cost' => 4500, 'min' => 20, 'main' => 65, 'store' => 8],
            ['sku' => 'SNK-001', 'name' => 'Keripik Singkong Balado', 'category' => 'Snacks', 'supplier' => 'CV Laut Timur Distribusi', 'price' => 12000, 'cost' => 7800, 'min' => 18, 'main' => 50, 'store' => 11],
            ['sku' => 'SNK-002', 'name' => 'Biskuit Coklat Family Pack', 'category' => 'Snacks', 'supplier' => 'CV Laut Timur Distribusi', 'price' => 15000, 'cost' => 10000, 'min' => 15, 'main' => 40, 'store' => 6],
            ['sku' => 'FOO-001', 'name' => 'Mie Instan Goreng', 'category' => 'Instant Food', 'supplier' => 'PT Pangan Sentosa', 'price' => 4500, 'cost' => 2900, 'min' => 40, 'main' => 120, 'store' => 26],
            ['sku' => 'HOU-001', 'name' => 'Sabun Cuci Piring 800ml', 'category' => 'Household', 'supplier' => 'PT Sumber Makmur', 'price' => 18000, 'cost' => 12500, 'min' => 10, 'main' => 22, 'store' => 4],
        ];

        $productMap = collect();

        foreach ($products as $item) {
            $product = Product::create([
                'company_id' => $company->id,
                'category_id' => $categories[$item['category']]->id,
                'supplier_id' => $suppliers[$item['supplier']]->id,
                'sku' => $item['sku'],
                'name' => $item['name'],
                'description' => 'Dummy product seeded for testing dashboard and transaction flow.',
                'price' => $item['price'],
                'cost_price' => $item['cost'],
                'minimum_stock' => $item['min'],
                'is_active' => true,
            ]);

            $productMap[$item['sku']] = $product;

            $mainStock = Stock::create([
                'company_id' => $company->id,
                'product_id' => $product->id,
                'warehouse_id' => $mainWarehouse->id,
                'quantity' => $item['main'],
            ]);

            $storeStock = Stock::create([
                'company_id' => $company->id,
                'product_id' => $product->id,
                'warehouse_id' => $storeWarehouse->id,
                'quantity' => $item['store'],
            ]);

            $this->createStockMovement($company->id, $product->id, $mainWarehouse->id, $staffB->id, 'opening_balance', $item['main'], 'Initial stock at main warehouse', now()->subDays(35));
            $this->createStockMovement($company->id, $product->id, $storeWarehouse->id, $staffB->id, 'opening_balance', $item['store'], 'Initial stock at front store', now()->subDays(35));

            if ($storeStock->quantity <= $product->minimum_stock) {
                $this->createAuditLog($company->id, $owner->id, 'stock.low_detected', $product::class, $product->id, ['warehouse' => $storeWarehouse->name, 'quantity' => $storeStock->quantity], now()->subDay());
            }
        }

        $transactions = [
            ['days' => 12, 'user' => $staffA, 'customer' => 'Budi', 'items' => [['BEV-001', 12], ['FOO-001', 18], ['SNK-001', 6]], 'tax' => 5000, 'discount' => 2000],
            ['days' => 9, 'user' => $staffA, 'customer' => 'Maya', 'items' => [['BEV-002', 10], ['SNK-002', 5]], 'tax' => 3000, 'discount' => 0],
            ['days' => 6, 'user' => $staffA, 'customer' => 'Andi', 'items' => [['FOO-001', 24], ['HOU-001', 3]], 'tax' => 4000, 'discount' => 1500],
            ['days' => 3, 'user' => $staffA, 'customer' => 'Sari', 'items' => [['BEV-001', 8], ['BEV-002', 7], ['SNK-001', 4]], 'tax' => 3500, 'discount' => 1000],
            ['days' => 1, 'user' => $staffA, 'customer' => 'Rizal', 'items' => [['HOU-001', 2], ['SNK-002', 6], ['FOO-001', 15]], 'tax' => 4500, 'discount' => 0],
        ];

        foreach ($transactions as $index => $seededTransaction) {
            $transactionDate = now()->subDays($seededTransaction['days']);
            $subtotal = 0;

            foreach ($seededTransaction['items'] as [$sku, $qty]) {
                $subtotal += $productMap[$sku]->price * $qty;
            }

            $transaction = Transaction::create([
                'company_id' => $company->id,
                'user_id' => $seededTransaction['user']->id,
                'warehouse_id' => $storeWarehouse->id,
                'invoice_number' => 'DEMO-INV-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'customer_name' => $seededTransaction['customer'],
                'customer_email' => Str::slug($seededTransaction['customer']) . '@customer.test',
                'status' => 'completed',
                'subtotal' => $subtotal,
                'tax_amount' => $seededTransaction['tax'],
                'discount_amount' => $seededTransaction['discount'],
                'total_amount' => $subtotal + $seededTransaction['tax'] - $seededTransaction['discount'],
                'notes' => 'Dummy seeded POS transaction',
                'transacted_at' => $transactionDate,
            ]);

            $transaction->forceFill([
                'created_at' => $transactionDate,
                'updated_at' => $transactionDate,
            ])->saveQuietly();

            foreach ($seededTransaction['items'] as [$sku, $qty]) {
                $product = $productMap[$sku];
                $lineTotal = $product->price * $qty;

                $detail = TransactionDetail::create([
                    'company_id' => $company->id,
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                    'line_total' => $lineTotal,
                ]);

                $detail->forceFill([
                    'created_at' => $transactionDate,
                    'updated_at' => $transactionDate,
                ])->saveQuietly();

                $storeStock = Stock::where('company_id', $company->id)
                    ->where('product_id', $product->id)
                    ->where('warehouse_id', $storeWarehouse->id)
                    ->firstOrFail();

                $storeStock->decrement('quantity', $qty);
                $storeStock->refresh();

                $this->createStockMovement($company->id, $product->id, $storeWarehouse->id, $seededTransaction['user']->id, 'sale', -$qty, 'Reduced by seeded demo transaction', $transactionDate, Transaction::class, $transaction->id);
            }

            $this->createAuditLog($company->id, $seededTransaction['user']->id, 'transaction.created', Transaction::class, $transaction->id, ['invoice_number' => $transaction->invoice_number], $transactionDate);
        }

        $this->createAuditLog($company->id, $owner->id, 'company.updated', Company::class, $company->id, ['field' => 'settings'], now()->subDays(8));
        $this->createAuditLog($company->id, $owner->id, 'subscription.checkout', Subscription::class, $subscription->id, ['plan' => $plan->name], now()->subDays(20));
        $this->createAuditLog($company->id, $owner->id, 'user.invited', User::class, $staffA->id, ['email' => $staffA->email], now()->subDays(18));
        $this->createAuditLog($company->id, $owner->id, 'user.invited', User::class, $staffB->id, ['email' => $staffB->email], now()->subDays(17));
    }

    protected function seedSecondaryDemoCompany(Role $ownerRole, Role $staffRole, Plan $plan): void
    {
        $company = Company::create([
            'name' => 'Borneo Wholesale Hub',
            'slug' => 'borneo-wholesale-hub',
            'email' => 'admin@borneo.test',
            'phone' => '0852-0000-1111',
            'city' => 'Balikpapan',
            'country' => 'Indonesia',
            'timezone' => 'Asia/Makassar',
            'currency' => 'IDR',
        ]);

        $owner = User::create([
            'company_id' => $company->id,
            'role_id' => $ownerRole->id,
            'name' => 'Second Owner',
            'email' => 'second-owner@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'company_id' => $company->id,
            'role_id' => $staffRole->id,
            'name' => 'Second Staff',
            'email' => 'second-staff@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'invited_by' => $owner->id,
            'email_verified_at' => now(),
        ]);

        $subscription = Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'gateway' => 'xendit',
            'gateway_reference' => 'XEN-DEMO-100',
            'status' => 'active',
            'starts_at' => now()->subDays(5),
            'ends_at' => now()->addDays(25),
            'trial_ends_at' => null,
            'meta' => ['checkout_url' => '#'],
        ]);

        Payment::create([
            'company_id' => $company->id,
            'subscription_id' => $subscription->id,
            'provider' => 'xendit',
            'provider_reference' => 'XEN-PAY-100',
            'amount' => $plan->price,
            'currency' => 'IDR',
            'status' => 'paid',
            'paid_at' => now()->subDays(5),
            'payload' => ['note' => 'Secondary company payment history'],
        ]);
    }

    protected function createStockMovement(int $companyId, int $productId, int $warehouseId, ?int $userId, string $type, int $quantity, string $notes, Carbon $date, ?string $referenceType = null, ?int $referenceId = null): void
    {
        $movement = StockMovement::create([
            'company_id' => $companyId,
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'user_id' => $userId,
            'type' => $type,
            'quantity' => $quantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
        ]);

        $movement->forceFill([
            'created_at' => $date,
            'updated_at' => $date,
        ])->saveQuietly();
    }

    protected function createAuditLog(int $companyId, ?int $userId, string $action, ?string $auditableType, ?int $auditableId, array $metadata, Carbon $date): void
    {
        $log = AuditLog::create([
            'company_id' => $companyId,
            'user_id' => $userId,
            'action' => $action,
            'auditable_type' => $auditableType,
            'auditable_id' => $auditableId,
            'metadata' => $metadata,
            'ip_address' => '127.0.0.1',
        ]);

        $log->forceFill([
            'created_at' => $date,
            'updated_at' => $date,
        ])->saveQuietly();
    }
}
