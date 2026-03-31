<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
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

        $company = Company::firstOrCreate([
            'slug' => 'demo-inventory',
        ], [
            'name' => 'Demo Inventory Co',
            'email' => 'owner@example.com',
            'timezone' => 'Asia/Jakarta',
            'currency' => 'IDR',
        ]);

        $ownerRole = Role::where('slug', Role::OWNER)->firstOrFail();
        $freePlan = Plan::where('slug', 'free')->firstOrFail();

        $user = User::updateOrCreate([
            'email' => 'owner@example.com',
        ], [
            'company_id' => $company->id,
            'role_id' => $ownerRole->id,
            'name' => 'Owner User',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        Subscription::firstOrCreate([
            'company_id' => $company->id,
            'plan_id' => $freePlan->id,
        ], [
            'gateway' => 'midtrans',
            'gateway_reference' => Str::uuid()->toString(),
            'status' => 'active',
            'starts_at' => now(),
            'trial_ends_at' => now()->addDays(14),
            'ends_at' => now()->addMonth(),
        ]);
    }
}
