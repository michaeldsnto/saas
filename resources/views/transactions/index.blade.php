<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs uppercase tracking-[0.28em] text-cyan-300 sm:text-sm sm:tracking-[0.3em]">POS</p>
            <h2 class="text-2xl font-semibold sm:text-3xl">Transactions</h2>
        </div>
    </x-slot>

    <div class="grid gap-4 xl:grid-cols-[0.9fr_1.1fr] xl:gap-6">
        <form method="POST" action="{{ route('transactions.store') }}" class="space-y-4 rounded-3xl border border-white/10 bg-white/5 p-5 sm:p-6">
            @csrf
            <div>
                <h3 class="text-xl font-semibold">Create Transaction</h3>
                <p class="mt-2 text-sm text-slate-400">Build a quick POS transaction without forcing mobile users to fight a dense form.</p>
            </div>

            <div><x-input-label for="warehouse_id" value="Warehouse" /><select id="warehouse_id" name="warehouse_id" class="mt-1 block w-full rounded-2xl border-slate-700 bg-slate-950/70 text-slate-100">@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>@endforeach</select></div>
            <div><x-input-label for="customer_name" value="Customer Name" /><x-text-input id="customer_name" name="customer_name" class="mt-1 block w-full" /></div>
            <div class="grid gap-4 md:grid-cols-2"><div><x-input-label for="tax_amount" value="Tax" /><x-text-input id="tax_amount" name="tax_amount" type="number" step="0.01" class="mt-1 block w-full" value="0" /></div><div><x-input-label for="discount_amount" value="Discount" /><x-text-input id="discount_amount" name="discount_amount" type="number" step="0.01" class="mt-1 block w-full" value="0" /></div></div>

            <div class="space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <h4 class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-400">Quick Items</h4>
                    <span class="text-xs text-slate-500">Top 5 products</span>
                </div>
                @foreach($products->take(5) as $index => $product)
                    <div class="grid gap-3 rounded-2xl bg-slate-950/60 p-4 md:grid-cols-[1fr_120px]">
                        <div>
                            <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $product->id }}">
                            <div class="font-medium">{{ $product->name }}</div>
                            <div class="text-xs text-slate-400">Stock: {{ $product->stocks->sum('quantity') }}</div>
                        </div>
                        <div><x-input-label for="qty_{{ $index }}" value="Quantity" /><x-text-input id="qty_{{ $index }}" name="items[{{ $index }}][quantity]" type="number" class="mt-1 block w-full" value="1" /></div>
                    </div>
                @endforeach
            </div>

            <x-primary-button class="justify-center sm:justify-start">Create Transaction</x-primary-button>
        </form>

        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 sm:p-6">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-xl font-semibold">Transaction History</h3>
                    <p class="mt-1 text-sm text-slate-400">Recent sales activity for your current company.</p>
                </div>
            </div>

            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-full text-left text-sm">
                    <thead class="text-slate-400"><tr><th class="pb-3">Invoice</th><th class="pb-3">Warehouse</th><th class="pb-3">Total</th></tr></thead>
                    <tbody class="divide-y divide-white/5">@foreach($transactions as $transaction)<tr><td class="py-3"><a href="{{ route('transactions.show', $transaction) }}" class="font-medium text-cyan-300">{{ $transaction->invoice_number }}</a></td><td class="py-3">{{ $transaction->warehouse?->name }}</td><td class="py-3">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td></tr>@endforeach</tbody>
                </table>
            </div>

            <div class="mobile-card-grid md:hidden">
                @forelse($transactions as $transaction)
                    <article class="mobile-data-card">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <a href="{{ route('transactions.show', $transaction) }}" class="font-semibold text-cyan-300">{{ $transaction->invoice_number }}</a>
                                <p class="mt-1 text-xs text-slate-400">{{ $transaction->warehouse?->name ?? 'No warehouse' }}</p>
                            </div>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs text-slate-200">Sale</span>
                        </div>
                        <p class="mt-4 text-lg font-semibold">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                    </article>
                @empty
                    <div class="mobile-data-card text-sm text-slate-400">No transactions yet.</div>
                @endforelse
            </div>

            <div class="mt-4">{{ $transactions->links() }}</div>
        </div>
    </div>
</x-app-layout>