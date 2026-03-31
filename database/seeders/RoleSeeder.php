<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Owner', 'slug' => Role::OWNER, 'description' => 'Company administrator with billing and settings access.'],
            ['name' => 'Staff', 'slug' => Role::STAFF, 'description' => 'Operational staff for inventory and sales.'],
        ] as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
