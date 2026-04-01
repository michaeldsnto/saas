<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Smart Inventory SaaS') }}</title>
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-slate-950 text-slate-100 antialiased">
    @php
        $subscriptionService = app(\App\Services\SubscriptionService::class);
        $company = auth()->user()?->company;
        $planAlerts = $company ? $subscriptionService->usageAlerts($company) : [];
        $activePlan = $company ? $subscriptionService->currentPlan($company) : null;
        $links = [
            ['route' => 'dashboard', 'label' => 'Dashboard', 'short' => 'Home'],
            ['route' => 'products.index', 'label' => 'Products', 'short' => 'Products'],
            ['route' => 'categories.index', 'label' => 'Categories', 'short' => 'Categories'],
            ['route' => 'suppliers.index', 'label' => 'Suppliers', 'short' => 'Suppliers'],
            ['route' => 'warehouses.index', 'label' => 'Warehouses', 'short' => 'Warehouses'],
            ['route' => 'transactions.index', 'label' => 'Transactions', 'short' => 'Sales'],
            ['route' => 'billing.index', 'label' => 'Billing', 'short' => 'Billing'],
            ['route' => 'company.settings.edit', 'label' => 'Company', 'short' => 'Company'],
            ['route' => 'company.users.index', 'label' => 'Users', 'short' => 'Users'],
            ['route' => 'audit.index', 'label' => 'Audit Logs', 'short' => 'Audit'],
            ['route' => 'profile.edit', 'label' => 'Profile', 'short' => 'Profile'],
        ];

        $mobileLinks = [
            ['route' => 'dashboard', 'label' => 'Dashboard'],
            ['route' => 'products.index', 'label' => 'Products'],
            ['route' => 'transactions.index', 'label' => 'Sales'],
            ['route' => 'billing.index', 'label' => 'Billing'],
        ];
    @endphp

    <div
        x-data="{ mobileNavOpen: false }"
        class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(56,189,248,0.2),_transparent_32%),radial-gradient(circle_at_bottom_right,_rgba(14,165,233,0.14),_transparent_28%),linear-gradient(180deg,_#020617,_#111827)]"
    >
        <div class="lg:hidden sticky top-0 z-30 border-b border-white/10 bg-slate-950/85 backdrop-blur-xl">
            <div class="flex items-center justify-between gap-3 px-4 py-3">
                <button
                    type="button"
                    x-on:click="mobileNavOpen = true"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-slate-100"
                    aria-label="Open navigation"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                </button>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-[11px] uppercase tracking-[0.28em] text-cyan-300">{{ $company?->name ?? 'Workspace' }}</p>
                    <p class="truncate text-base font-semibold text-white">Smart Inventory</p>
                </div>
                @if ($activePlan)
                    <div class="rounded-2xl bg-cyan-300 px-3 py-2 text-xs font-semibold text-slate-950">
                        {{ $activePlan->name }}
                    </div>
                @endif
            </div>
        </div>

        <div
            x-cloak
            x-show="mobileNavOpen"
            x-transition.opacity
            class="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-sm lg:hidden"
            x-on:click="mobileNavOpen = false"
        ></div>

        <aside
            x-cloak
            x-show="mobileNavOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="-translate-x-full opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="-translate-x-full opacity-0"
            class="fixed inset-y-0 left-0 z-50 w-[88vw] max-w-[340px] overflow-y-auto border-r border-white/10 bg-slate-950/95 p-5 shadow-2xl backdrop-blur-xl lg:hidden"
        >
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] uppercase tracking-[0.28em] text-cyan-300">{{ $company?->name ?? 'Workspace' }}</p>
                    <h1 class="mt-2 text-2xl font-semibold">Smart Inventory</h1>
                    <p class="mt-2 text-sm text-slate-400">Fast navigation for phone screens without the desktop clutter.</p>
                </div>
                <button
                    type="button"
                    x-on:click="mobileNavOpen = false"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-slate-200"
                    aria-label="Close navigation"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
                    </svg>
                </button>
            </div>

            <div class="mt-5 rounded-3xl border border-cyan-400/20 bg-cyan-400/10 p-4">
                <p class="text-xs uppercase tracking-[0.2em] text-cyan-200">Current plan</p>
                <div class="mt-2 flex items-center justify-between gap-3">
                    <span class="text-lg font-semibold">{{ $activePlan?->name ?? 'No Active Plan' }}</span>
                    <span class="rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-slate-950">{{ strtoupper($company?->currency ?? 'IDR') }}</span>
                </div>
            </div>

            <nav class="mt-6 space-y-2 text-sm">
                @foreach ($links as $link)
                    @if (Route::has($link['route']))
                        <a
                            href="{{ route($link['route']) }}"
                            x-on:click="mobileNavOpen = false"
                            class="flex items-center justify-between rounded-2xl px-4 py-3 {{ request()->routeIs($link['route']) ? 'bg-cyan-400 text-slate-950' : 'bg-white/5 text-slate-200 hover:bg-white/10' }}"
                        >
                            <span>{{ $link['label'] }}</span>
                            <span class="text-xs opacity-70">{{ $link['short'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>

            <form method="POST" action="{{ route('logout') }}" class="mt-6">
                @csrf
                <button class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-left text-sm text-slate-200 transition hover:bg-white/10">
                    Sign out
                </button>
            </form>
        </aside>

        <div class="mx-auto grid min-h-screen w-full max-w-[1600px] gap-4 px-3 py-3 sm:px-5 sm:py-5 xl:px-8 2xl:max-w-[1800px] lg:grid-cols-[280px_minmax(0,1fr)] lg:gap-6 lg:py-6">
            <aside class="sticky top-6 hidden h-[calc(100vh-3rem)] overflow-y-auto rounded-[2rem] border border-white/10 bg-slate-900/75 p-6 shadow-2xl backdrop-blur lg:block">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-cyan-300">{{ $company?->name ?? 'Workspace' }}</p>
                    <h1 class="mt-2 text-2xl font-semibold">Smart Inventory</h1>
                    <p class="mt-2 text-sm text-slate-400">Multi-tenant inventory, billing, analytics, and prediction in one Laravel app.</p>
                </div>

                <nav class="mt-8 space-y-2 text-sm">
                    @foreach ($links as $link)
                        @if (Route::has($link['route']))
                            <a
                                href="{{ route($link['route']) }}"
                                class="block rounded-2xl px-4 py-3 {{ request()->routeIs($link['route']) ? 'bg-cyan-400 text-slate-950' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}"
                            >
                                {{ $link['label'] }}
                            </a>
                        @endif
                    @endforeach
                </nav>

                <form method="POST" action="{{ route('logout') }}" class="mt-8">
                    @csrf
                    <button class="w-full rounded-2xl border border-white/10 px-4 py-3 text-left text-sm text-slate-300 transition hover:bg-white/5">
                        Sign out
                    </button>
                </form>
            </aside>

            <main class="space-y-4 rounded-[1.75rem] border border-white/10 bg-slate-900/55 p-4 shadow-2xl backdrop-blur sm:p-5 lg:space-y-6 lg:rounded-[2rem] lg:p-6 lg:pb-6 pb-24">
                @isset($header)
                    <header class="flex flex-col gap-3 border-b border-white/10 pb-4 lg:flex-row lg:items-center lg:justify-between lg:pb-6">
                        <div class="min-w-0">{{ $header }}</div>
                        <div class="hidden text-sm text-slate-400 lg:block">{{ now()->format('d M Y H:i') }} - {{ auth()->user()->name }}</div>
                    </header>
                @endisset

                @if ($activePlan)
                    <div class="flex flex-col gap-2 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-cyan-300 px-3 py-1 font-semibold text-slate-950">{{ $activePlan->name }} Plan</span>
                            <span class="text-slate-400">Current workspace plan is active.</span>
                        </div>
                        <span class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ auth()->user()->name }}</span>
                    </div>
                @endif

                @if (!empty($planAlerts))
                    <div class="rounded-2xl border border-amber-400/30 bg-amber-400/10 px-4 py-3 text-sm text-amber-100">
                        <p class="font-semibold">Plan warning</p>
                        <div class="mt-1 space-y-1">
                            @foreach ($planAlerts as $alert)
                                <p>
                                    {{ $alert['label'] }}: {{ $alert['used'] }} / {{ $alert['limit'] }} used.
                                    @if ($alert['status'] === 'limit-reached')
                                        Limit reached. Consider upgrading your plan.
                                    @else
                                        Only {{ $alert['remaining'] }} slot(s) remaining.
                                    @endif
                                </p>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (session('status'))
                    <div class="rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">
                        {{ session('status') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>

        <nav class="fixed inset-x-0 bottom-0 z-30 border-t border-white/10 bg-slate-950/90 px-3 pb-3 pt-2 backdrop-blur-xl lg:hidden">
            <div class="grid grid-cols-4 gap-2 rounded-[1.5rem] border border-white/10 bg-white/5 p-2">
                @foreach ($mobileLinks as $link)
                    @if (Route::has($link['route']))
                        <a
                            href="{{ route($link['route']) }}"
                            class="rounded-2xl px-3 py-2 text-center text-[11px] font-medium {{ request()->routeIs($link['route']) ? 'bg-cyan-300 text-slate-950' : 'text-slate-300' }}"
                        >
                            {{ $link['label'] }}
                        </a>
                    @endif
                @endforeach
            </div>
        </nav>
    </div>
</body>
</html>