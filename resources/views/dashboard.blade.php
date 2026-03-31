<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm uppercase tracking-[0.3em] text-cyan-300">Analytics</p>
            <h2 class="text-3xl font-semibold">Dashboard</h2>
        </div>
    </x-slot>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5"><p class="text-sm text-slate-400">Revenue</p><p class="mt-2 text-3xl font-semibold">Rp {{ number_format($analytics['revenue'], 0, ',', '.') }}</p></div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5"><p class="text-sm text-slate-400">Transactions</p><p class="mt-2 text-3xl font-semibold">{{ $analytics['sales_trend']->count() }}</p></div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5"><p class="text-sm text-slate-400">Stock Usage</p><p class="mt-2 text-3xl font-semibold">{{ $analytics['stock_usage'] }}</p></div>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5"><p class="text-sm text-slate-400">Low Stock SKUs</p><p class="mt-2 text-3xl font-semibold">{{ $lowStocks->count() }}</p></div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <section class="rounded-3xl border border-white/10 bg-white/5 p-6">
            <h3 class="text-lg font-semibold">Sales Trend</h3>
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
            <h3 class="text-lg font-semibold">AI Insights</h3>
            <div class="mt-4 space-y-3">
                @forelse ($analytics['insights'] as $insight)
                    <div class="rounded-2xl border border-cyan-400/20 bg-cyan-400/10 p-4 text-sm text-cyan-100">{{ $insight }}</div>
                @empty
                    <div class="rounded-2xl border border-white/10 bg-slate-950/60 p-4 text-sm text-slate-400">Predictions will appear after sales activity accumulates.</div>
                @endforelse
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
                            <td class="py-3 text-slate-300">{{ $predictions[$product->id]['estimated_run_out_date'] ?? 'Not enough data' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-slate-400">All tracked products are above minimum stock.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-app-layout>
