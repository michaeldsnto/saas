<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Support\Tenant\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_scoped_models_only_return_current_tenant_records(): void
    {
        $this->seed();

        $ownerRole = Role::where('slug', Role::OWNER)->firstOrFail();
        $companyA = Company::create(['name' => 'Alpha', 'slug' => 'alpha', 'timezone' => 'Asia/Jakarta', 'currency' => 'IDR']);
        $companyB = Company::create(['name' => 'Beta', 'slug' => 'beta', 'timezone' => 'Asia/Jakarta', 'currency' => 'IDR']);

        $userA = User::create(['company_id' => $companyA->id, 'role_id' => $ownerRole->id, 'name' => 'A', 'email' => 'a@example.com', 'password' => 'password', 'is_active' => true, 'email_verified_at' => now()]);
        User::create(['company_id' => $companyB->id, 'role_id' => $ownerRole->id, 'name' => 'B', 'email' => 'b@example.com', 'password' => 'password', 'is_active' => true, 'email_verified_at' => now()]);

        app(TenantManager::class)->set($companyA);
        $this->actingAs($userA);

        Category::create(['name' => 'Alpha Category']);
        Category::withoutGlobalScope('company')->create(['company_id' => $companyB->id, 'name' => 'Beta Category']);

        $this->get(route('categories.index'))->assertSee('Alpha Category')->assertDontSee('Beta Category');
    }
}
