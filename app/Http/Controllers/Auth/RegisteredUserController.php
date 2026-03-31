<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterTenantRequest;
use App\Models\Company;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterTenantRequest $request): RedirectResponse
    {
        $user = DB::transaction(function () use ($request) {
            $role = Role::firstOrCreate(['slug' => Role::OWNER], ['name' => 'Owner']);
            $plan = Plan::firstOrCreate(['slug' => 'free'], [
                'name' => 'Free',
                'price' => 0,
                'billing_cycle' => 'monthly',
                'product_limit' => 25,
                'warehouse_limit' => 1,
                'staff_limit' => 2,
                'features' => ['dashboard', 'inventory', 'basic_analytics'],
                'is_active' => true,
            ]);

            $companyName = (string) ($request->input('company_name') ?: $request->input('name') . ' Company');

            $company = Company::create([
                'name' => $companyName,
                'slug' => Str::slug($companyName) . '-' . Str::lower(Str::random(4)),
                'email' => $request->input('company_email') ?: $request->input('email'),
                'phone' => $request->input('phone'),
                'timezone' => 'Asia/Jakarta',
                'currency' => 'IDR',
            ]);

            $user = User::create([
                'company_id' => $company->id,
                'role_id' => $role->id,
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'password' => Hash::make($request->input('password')),
                'is_active' => true,
            ]);

            Subscription::create([
                'company_id' => $company->id,
                'plan_id' => $plan->id,
                'gateway' => 'midtrans',
                'gateway_reference' => Str::uuid()->toString(),
                'status' => 'trial',
                'starts_at' => now(),
                'trial_ends_at' => now()->addDays(14),
                'ends_at' => now()->addMonth(),
            ]);

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
