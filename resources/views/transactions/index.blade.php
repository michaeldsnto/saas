<x-app-layout>
    <x-slot name="header"><div><p class="text-sm uppercase tracking-[0.3em] text-cyan-300">POS</p><h2 class="text-3xl font-semibold">Transactions</h2></div></x-slot>
    <div class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
        <form method="POST" action="{{ route('transactions.store') }}" class="space-y-4 rounded-3xl border border-white/10 bg-white/5 p-6">@csrf
            <div><x-input-label for="warehouse_id" value="Warehouse" /><select id="warehouse_id" name="warehouse_id" class="mt-1 block w-full rounded-2xl border-slate-700 bg-slate-950/70 text-slate-100">@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>@endforeach</select></div>
            <div><x-input-label for="customer_name" value="Customer Name" /><x-text-input id="customer_name" name="customer_name" class="mt-1 block w-full" /></div>
            <div class="grid gap-4 md:grid-cols-2"><div><x-input-label for="tax_amount" value="Tax" /><x-text-input id="tax_amount" name="tax_amount" type="number" step="0.01" class="mt-1 block w-full" value="0" /></div><div><x-input-label for="discount_amount" value="Discount" /><x-text-input id="discount_amount" name="discount_amount" type="number" step="0.01" class="mt-1 block w-full" value="0" /></div></div>
            <div class="space-y-3">
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
            <x-primary-button>Create Transaction</x-primary-button>
        </form>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-6"><table class="min-w-full text-left text-sm"><thead class="text-slate-400"><tr><th class="pb-3">Invoice</th><th class="pb-3">Warehouse</th><th class="pb-3">Total</th></tr></thead><tbody class="divide-y divide-white/5">@foreach($transactions as $transaction)<tr><td class="py-3"><a href="{{ route('transactions.show', $transaction) }}" class="font-medium text-cyan-300">{{ $transaction->invoice_number }}</a></td><td class="py-3">{{ $transaction->warehouse?->name }}</td><td class="py-3">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td></tr>@endforeach</tbody></table><div class="mt-4">{{ $transactions->links() }}</div></div>
    </div>
</x-app-layout>
