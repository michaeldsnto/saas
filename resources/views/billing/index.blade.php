<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs uppercase tracking-[0.28em] text-cyan-300 sm:text-sm sm:tracking-[0.3em]">Billing</p>
            <h2 class="text-2xl font-semibold sm:text-3xl">Plans & Subscription</h2>
        </div>
    </x-slot>

    <section class="grid gap-4 xl:grid-cols-[0.95fr_1.05fr] xl:gap-6">
        <div class="rounded-3xl border border-cyan-400/20 bg-cyan-400/10 p-5 sm:p-6">
            <p class="text-sm uppercase tracking-[0.3em] text-cyan-200">Current Subscription</p>
            <h3 class="mt-3 text-2xl font-semibold sm:text-3xl">{{ $currentPlan?->name ?? 'No Active Plan' }}</h3>
            <p class="mt-2 text-sm text-cyan-100/80">Status: {{ strtoupper($subscription?->status ?? 'inactive') }}</p>
            <div class="mt-6 grid gap-3 sm:grid-cols-3 sm:gap-4">
                @forelse($usage as $item)
                    <div class="rounded-2xl bg-slate-950/40 p-4">
                        <p class="text-sm text-slate-400">{{ $item['label'] }}</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $item['used'] }} / {{ $item['limit'] }}</p>
                        <p class="mt-1 text-xs {{ $item['status'] === 'limit-reached' ? 'text-rose-300' : ($item['status'] === 'warning' ? 'text-amber-300' : 'text-slate-400') }}">Remaining {{ $item['remaining'] }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Plan usage is not available yet.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 sm:p-6">
            <h3 class="text-lg font-semibold">Why plans feel different now</h3>
            <div class="mt-4 space-y-3 text-sm text-slate-300">
                <p>Free, Pro, and Enterprise now show real usage numbers, so you can see how many products, warehouses, and staff slots are already used.</p>
                <p>When usage gets close to the limit, the warning color changes. When it reaches the limit, the app will block new records in that area.</p>
                <p>That means the difference between plans is no longer just a label in billing, but something visible in daily operation.</p>
            </div>
        </div>
    </section>

    <div class="grid gap-4 xl:grid-cols-3 xl:gap-6">
        @foreach($plans as $plan)
            @php
                $isCurrent = $currentPlan?->id === $plan->id;
                $isUpgrade = $currentPlan && $plan->price > $currentPlan->price;
                $isDowngrade = $currentPlan && $plan->price < $currentPlan->price;
            @endphp
            <form method="POST" action="{{ route('billing.checkout') }}" class="rounded-3xl border p-5 sm:p-6 {{ $isCurrent ? 'border-cyan-400/40 bg-cyan-400/10' : 'border-white/10 bg-white/5' }}">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-2xl font-semibold">{{ $plan->name }}</h3>
                        <p class="mt-2 text-sm text-slate-400">{{ implode(', ', $plan->features ?? []) }}</p>
                    </div>
                    @if($isCurrent)
                        <span class="rounded-full bg-cyan-300 px-3 py-1 text-xs font-semibold text-slate-950">Current</span>
                    @elseif($isUpgrade)
                        <span class="rounded-full bg-emerald-300 px-3 py-1 text-xs font-semibold text-slate-950">Upgrade</span>
                    @elseif($isDowngrade)
                        <span class="rounded-full bg-amber-300 px-3 py-1 text-xs font-semibold text-slate-950">Downgrade</span>
                    @endif
                </div>

                <p class="mt-5 text-3xl font-semibold sm:text-4xl">Rp {{ number_format($plan->price, 0, ',', '.') }}</p>
                <p class="mt-2 text-sm text-slate-400">{{ ucfirst($plan->billing_cycle) }}</p>

                <div class="mt-6 space-y-2 rounded-2xl bg-slate-950/40 p-4 text-sm text-slate-300">
                    <div class="flex items-center justify-between"><span>Products</span><span>{{ $plan->product_limit }}</span></div>
                    <div class="flex items-center justify-between"><span>Warehouses</span><span>{{ $plan->warehouse_limit }}</span></div>
                    <div class="flex items-center justify-between"><span>Staff</span><span>{{ $plan->staff_limit }}</span></div>
                </div>

                <div class="mt-4">
                    <x-input-label for="gateway_{{ $plan->id }}" value="Payment Gateway" />
                    <select id="gateway_{{ $plan->id }}" name="gateway" class="mt-1 block w-full rounded-2xl border-slate-700 bg-slate-950/70 text-slate-100">
                        <option value="midtrans">Midtrans</option>
                        <option value="xendit">Xendit</option>
                    </select>
                </div>

                <x-primary-button class="mt-6 w-full justify-center">{{ $isCurrent ? 'Renew / Recreate Plan' : 'Choose Plan' }}</x-primary-button>
            </form>
        @endforeach
    </div>

    <div class="grid gap-4 lg:grid-cols-[0.9fr_1.1fr] lg:gap-6">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 sm:p-6">
            <h3 class="text-lg font-semibold">Usage Detail</h3>
            <div class="mt-4 space-y-4">
                @foreach($usage as $item)
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
                            <span class="text-slate-400">{{ $item['used'] }} / {{ $item['limit'] }}</span>
                        </div>
                        <div class="h-3 overflow-hidden rounded-full bg-slate-800">
                            <div class="h-full rounded-full {{ $barColor }}" style="width: {{ $item['percentage'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 sm:p-6">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-lg font-semibold">Billing History</h3>
                <p class="text-sm text-slate-400">Latest payment activity for this company.</p>
            </div>

            <div class="mt-4 hidden overflow-x-auto md:block">
                <table class="min-w-full text-left text-sm">
                    <thead class="text-slate-400"><tr><th class="pb-3">Provider</th><th class="pb-3">Reference</th><th class="pb-3">Amount</th><th class="pb-3">Status</th></tr></thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($payments as $payment)
                            <tr>
                                <td class="py-3">{{ strtoupper($payment->provider) }}</td>
                                <td class="py-3">{{ $payment->provider_reference }}</td>
                                <td class="py-3">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td class="py-3 {{ $payment->status === 'paid' ? 'text-emerald-300' : 'text-amber-300' }}">{{ strtoupper($payment->status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-slate-400">No billing history yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mobile-card-grid mt-4 md:hidden">
                @forelse($payments as $payment)
                    <article class="mobile-data-card">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="font-semibold">{{ strtoupper($payment->provider) }}</h4>
                                <p class="mt-1 text-xs text-slate-400">{{ $payment->provider_reference }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $payment->status === 'paid' ? 'bg-emerald-300 text-slate-950' : 'bg-amber-300 text-slate-950' }}">{{ strtoupper($payment->status) }}</span>
                        </div>
                        <p class="mt-4 text-lg font-semibold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                    </article>
                @empty
                    <div class="mobile-data-card text-sm text-slate-400">No billing history yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>