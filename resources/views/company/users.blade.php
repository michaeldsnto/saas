<x-app-layout>
    <x-slot name="header"><div><p class="text-sm uppercase tracking-[0.3em] text-cyan-300">Company</p><h2 class="text-3xl font-semibold">User Management</h2></div></x-slot>
    <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
        <form method="POST" action="{{ route('company.users.store') }}" class="space-y-4 rounded-3xl border border-white/10 bg-white/5 p-6">
            @csrf
            <div><x-input-label for="name" value="Name" /><x-text-input id="name" name="name" class="mt-1 block w-full" /></div>
            <div><x-input-label for="email" value="Email" /><x-text-input id="email" name="email" class="mt-1 block w-full" /></div>
            <div><x-input-label for="phone" value="Phone" /><x-text-input id="phone" name="phone" class="mt-1 block w-full" /></div>
            <div><x-input-label for="job_title" value="Job Title" /><x-text-input id="job_title" name="job_title" class="mt-1 block w-full" /></div>
            <div><x-input-label for="role_id" value="Role" /><select id="role_id" name="role_id" class="mt-1 block w-full rounded-2xl border-slate-700 bg-slate-950/70 text-slate-100">@foreach($roles as $role)<option value="{{ $role->id }}">{{ $role->name }}</option>@endforeach</select></div>
            <x-primary-button>Create Staff User</x-primary-button>
        </form>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
            <table class="min-w-full text-left text-sm"><thead class="text-slate-400"><tr><th class="pb-3">Name</th><th class="pb-3">Email</th><th class="pb-3">Role</th></tr></thead><tbody class="divide-y divide-white/5">@foreach($users as $user)<tr><td class="py-3">{{ $user->name }}</td><td class="py-3">{{ $user->email }}</td><td class="py-3">{{ $user->role?->name }}</td></tr>@endforeach</tbody></table>
        </div>
    </div>
</x-app-layout>
