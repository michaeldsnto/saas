<?php

namespace App\Http\Controllers;

use App\Http\Requests\InviteUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        return view('company.users', [
            'users' => User::query()->with('role')->latest()->get(),
            'roles' => Role::query()->get(),
        ]);
    }

    public function store(InviteUserRequest $request, SubscriptionService $subscriptionService, AuditLogService $auditLogService): RedirectResponse
    {
        abort_unless($subscriptionService->withinLimit($request->user()->company, 'staff', User::query()->count()), 422, 'Your plan staff limit has been reached.');

        $user = User::create([
            'company_id' => $request->user()->company_id,
            'role_id' => $request->integer('role_id'),
            'name' => $request->string('name'),
            'email' => $request->string('email'),
            'phone' => $request->string('phone'),
            'job_title' => $request->string('job_title'),
            'password' => Hash::make(Str::password(12)),
            'is_active' => true,
            'invited_by' => $request->user()->id,
        ]);

        $auditLogService->record('user.invited', $user, ['email' => $user->email], $request);

        return back()->with('status', 'Staff account created. You can replace the temporary password flow with email invites later.');
    }
}
