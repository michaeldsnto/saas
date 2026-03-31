<x-app-layout>
    <x-slot name="header"><div><p class="text-sm uppercase tracking-[0.3em] text-cyan-300">POS</p><h2 class="text-3xl font-semibold">Transaction Detail</h2></div></x-slot>
    <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
        <div class="grid gap-4 md:grid-cols-4"><div><p class="text-sm text-slate-400">Invoice</p><p class="mt-2 font-semibold">{{ $transaction->invoice_number }}</p></div><div><p class="text-sm text-slate-400">Warehouse</p><p class="mt-2 font-semibold">{{ $transaction->warehouse?->name }}</p></div><div><p class="text-sm text-slate-400">Cashier</p><p class="mt-2 font-semibold">{{ $transaction->user?->name }}</p></div><div><p class="text-sm text-slate-400">Total</p><p class="mt-2 font-semibold">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p></div></div>
        <table class="mt-6 min-w-full text-left text-sm"><thead class="text-slate-400"><tr><th class="pb-3">Product</th><th class="pb-3">Qty</th><th class="pb-3">Unit Price</th><th class="pb-3">Line Total</th></tr></thead><tbody class="divide-y divide-white/5">@foreach($transaction->details as $detail)<tr><td class="py-3">{{ $detail->product->name }}</td><td class="py-3">{{ $detail->quantity }}</td><td class="py-3">{{ $detail->unit_price }}</td><td class="py-3">{{ $detail->line_total }}</td></tr>@endforeach</tbody></table>
    </div>
</x-app-layout>
