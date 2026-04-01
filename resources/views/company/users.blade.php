<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs uppercase tracking-[0.28em] text-cyan-300 sm:text-sm sm:tracking-[0.3em]">Company</p>
            <h2 class="text-2xl font-semibold sm:text-3xl">User Management</h2>
        </div>
    </x-slot>

    <div class="grid gap-4 lg:grid-cols-[0.9fr_1.1fr] lg:gap-6">
        <form method="POST" action="{{ route('company.users.store') }}" class="space-y-4 rounded-3xl border border-white/10 bg-white/5 p-5 sm:p-6">
            @csrf
            <div>
                <h3 class="text-xl font-semibold">Invite Staff</h3>
                <p class="mt-2 text-sm text-slate-400">Create team accounts with a form that still feels comfortable on smaller screens.</p>
            </div>
            <div><x-input-label for="name" value="Name" /><x-text-input id="name" name="name" class="mt-1 block w-full" /></div>
            <div><x-input-label for="email" value="Email" /><x-text-input id="email" name="email" class="mt-1 block w-full" /></div>
            <div><x-input-label for="phone" value="Phone" /><x-text-input id="phone" name="phone" class="mt-1 block w-full" /></div>
            <div><x-input-label for="job_title" value="Job Title" /><x-text-input id="job_title" name="job_title" class="mt-1 block w-full" /></div>
            <div><x-input-label for="role_id" value="Role" /><select id="role_id" name="role_id" class="mt-1 block w-full rounded-2xl border-slate-700 bg-slate-950/70 text-slate-100">@foreach($roles as $role)<option value="{{ $role->id }}">{{ $role->name }}</option>@endforeach</select></div>
            <x-primary-button class="justify-center sm:justify-start">Create Staff User</x-primary-button>
        </form>

        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 sm:p-6">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-xl font-semibold">Team Members</h3>
                    <p class="mt-1 text-sm text-slate-400">Owner and staff accounts inside the current tenant.</p>
                </div>
            </div>

            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-full text-left text-sm"><thead class="text-slate-400"><tr><th class="pb-3">Name</th><th class="pb-3">Email</th><th class="pb-3">Role</th></tr></thead><tbody class="divide-y divide-white/5">@foreach($users as $user)<tr><td class="py-3">{{ $user->name }}</td><td class="py-3">{{ $user->email }}</td><td class="py-3">{{ $user->role?->name }}</td></tr>@endforeach</tbody></table>
            </div>

            <div class="mobile-card-grid md:hidden">
                @forelse($users as $user)
                    <article class="mobile-data-card">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="font-semibold">{{ $user->name }}</h4>
                                <p class="mt-1 text-xs text-slate-400">{{ $user->email }}</p>
                            </div>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs text-slate-200">{{ $user->role?->name }}</span>
                        </div>
                        @if($user->job_title || $user->phone)
                            <div class="mt-4 space-y-1 text-sm text-slate-300">
                                @if($user->job_title)
                                    <p>{{ $user->job_title }}</p>
                                @endif
                                @if($user->phone)
                                    <p class="text-slate-400">{{ $user->phone }}</p>
                                @endif
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="mobile-data-card text-sm text-slate-400">No users found yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>