<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs uppercase tracking-[0.28em] text-cyan-300 sm:text-sm sm:tracking-[0.3em]">Security</p>
            <h2 class="text-2xl font-semibold sm:text-3xl">Audit Logs</h2>
        </div>
    </x-slot>

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 sm:p-6">
        <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-xl font-semibold">Recent Activity</h3>
                <p class="mt-1 text-sm text-slate-400">Important actions tracked for accountability and debugging.</p>
            </div>
        </div>

        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full text-left text-sm"><thead class="text-slate-400"><tr><th class="pb-3">Action</th><th class="pb-3">User</th><th class="pb-3">Entity</th><th class="pb-3">Time</th></tr></thead><tbody class="divide-y divide-white/5">@foreach($logs as $log)<tr><td class="py-3">{{ $log->action }}</td><td class="py-3">{{ $log->user?->name }}</td><td class="py-3">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</td><td class="py-3">{{ $log->created_at }}</td></tr>@endforeach</tbody></table>
        </div>

        <div class="mobile-card-grid md:hidden">
            @forelse($logs as $log)
                <article class="mobile-data-card">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h4 class="font-semibold">{{ $log->action }}</h4>
                            <p class="mt-1 text-xs text-slate-400">{{ $log->user?->name ?? 'System' }}</p>
                        </div>
                        <span class="rounded-full bg-white/10 px-3 py-1 text-[11px] text-slate-200">{{ class_basename($log->auditable_type) }}</span>
                    </div>
                    <div class="mt-4 space-y-1 text-sm text-slate-300">
                        <p>{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</p>
                        <p class="text-slate-400">{{ $log->created_at }}</p>
                    </div>
                </article>
            @empty
                <div class="mobile-data-card text-sm text-slate-400">No audit logs yet.</div>
            @endforelse
        </div>

        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
</x-app-layout>