<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm uppercase tracking-[0.3em] text-cyan-300">Analytics</p>
            <h2 class="text-3xl font-semibold">Dashboard</h2>
        </div>
    </x-slot>

    <section class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <div class="rounded-3xl border border-cyan-400/20 bg-cyan-400/10 p-6">
            <p class="text-sm uppercase tracking-[0.3em] text-cyan-200">Current Plan</p>
            <div class="mt-3 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <h3 class="text-3xl font-semibold">{{ $subscription?->plan?->name ?? 'No Active Plan' }}</h3>
                    <p class="mt-2 text-sm text-cyan-100/80">Status: {{ strtoupper($subscription?->status ?? 'inactive') }} @if($subscription?->ends_at) � Renews / ends {{ $subscription->ends_at->format('d M Y') }} @endif</p>
                </div>
                <a href="{{ route('billing.index') }}" class="rounded-2xl border border-cyan-200/20 px-4 py-2 text-sm text-cyan-100 transition hover:bg-cyan-300/10">Manage Billing</a>
            </div>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
            <h3 class="text-lg font-semibold">Plan Usage</h3>
            <div class="mt-4 space-y-4">
                @forelse ($usage as $item)
                    @php
                        $barColor = match($item['status']) {
                            'limit-reached' => 'bg-rose-400',
                            'warning' => 'bg-amber-400',
                            default => 'bg-emerald-400',
                        };
                    @endphp
                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span>{{ $item['label'] }}</span>
                            <span class="text-slate-400">{{ $item['used'] }} / {{ $item['limit'] }} used</span>
                        </div>
                        <div class="h-3 overflow-hidden rounded-full bg-slate-800">
                            <div class="h-full rounded-full {{ $barColor }}" style="width: {{ $item['percentage'] }}%"></div>
                        </div>
                        <p class="mt-2 text-xs {{ $item['status'] === 'limit-reached' ? 'text-rose-300' : ($item['status'] === 'warning' ? 'text-amber-300' : 'text-slate-400') }}">
                            @if($item['status'] === 'limit-reached')
                                Limit reached. Upgrade your plan to add more {{ strtolower($item['label']) }}.
                            @elseif($item['status'] === 'warning')
                                Almost full. Remaining {{ $item['remaining'] }} {{ strtolower($item['label']) }} slot(s).
                            @else
                                Remaining {{ $item['remaining'] }} {{ strtolower($item['label']) }} slot(s).
                            @endif
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">No plan usage available yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5"><p class="text-sm text-slate-400">Revenue</p><p class="mt-2 text-3xl font-semibold">Rp {{ number_format($analytics['revenue'], 0, ',', '.') }}</p></div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5"><p class="text-sm text-slate-400">Transactions</p><p class="mt-2 text-3xl font-semibold">{{ $analytics['sales_trend']->count() }}</p></div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5"><p class="text-sm text-slate-400">Stock Usage</p><p class="mt-2 text-3xl font-semibold">{{ $analytics['stock_usage'] }}</p></div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5"><p class="text-sm text-slate-400">Low Stock SKUs</p><p class="mt-2 text-3xl font-semibold">{{ $lowStocks->count() }}</p></div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <section class="rounded-3xl border border-white/10 bg-white/5 p-6">
            <div class="flex items-center justify-between gap-4">
                <h3 class="text-lg font-semibold">Sales Trend</h3>
                @unless($canUseAdvancedAnalytics)
                    <span class="rounded-full bg-amber-300 px-3 py-1 text-xs font-semibold text-slate-950">Free analytics mode</span>
                @endunless
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($analytics['sales_trend'] as $row)
                    <div class="flex items-center justify-between rounded-2xl bg-slate-950/60 px-4 py-3">
                        <span>{{ $row->day }}</span>
                        <span class="font-semibold">Rp {{ number_format($row->revenue, 0, ',', '.') }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">No transactions yet.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-3xl border border-white/10 bg-white/5 p-6">
            <div class="flex items-center justify-between gap-4">
                <h3 class="text-lg font-semibold">AI Insights</h3>
                @if($canUsePrediction)
                    <span class="rounded-full bg-emerald-300 px-3 py-1 text-xs font-semibold text-slate-950">Pro feature active</span>
                @else
                    <span class="rounded-full bg-amber-300 px-3 py-1 text-xs font-semibold text-slate-950">Upgrade to Pro</span>
                @endif
            </div>
            <div class="mt-4 space-y-3">
                @if(!$canUsePrediction)
                    <div class="rounded-2xl border border-amber-400/20 bg-amber-400/10 p-4 text-sm text-amber-100">AI stock prediction is locked on the Free plan. Upgrade to Pro or Enterprise to unlock smarter run-out insights.</div>
                @else
                    @forelse ($analytics['insights'] as $insight)
                        <div class="rounded-2xl border border-cyan-400/20 bg-cyan-400/10 p-4 text-sm text-cyan-100">{{ $insight }}</div>
                    @empty
                        <div class="rounded-2xl border border-white/10 bg-slate-950/60 p-4 text-sm text-slate-400">Predictions will appear after sales activity accumulates.</div>
                    @endforelse
                @endif
            </div>
        </section>
    </div>

    <section class="rounded-3xl border border-white/10 bg-white/5 p-6">
        <h3 class="text-lg font-semibold">Low Stock Watchlist</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="text-slate-400"><tr><th class="pb-3">Product</th><th class="pb-3">Stock</th><th class="pb-3">Minimum</th><th class="pb-3">Prediction</th></tr></thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($lowStocks as $product)
                        <tr>
                            <td class="py-3">{{ $product->name }}</td>
                            <td class="py-3">{{ $product->totalStock() }}</td>
                            <td class="py-3">{{ $product->minimum_stock }}</td>
                            <td class="py-3 text-slate-300">
                                @if($canUsePrediction)
                                    {{ $predictions[$product->id]['estimated_run_out_date'] ?? 'Not enough data' }}
                                @else
                                    Locked on Free plan
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-slate-400">All tracked products are above minimum stock.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-app-layout>
