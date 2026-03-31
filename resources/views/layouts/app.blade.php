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
    <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(56,189,248,0.18),_transparent_30%),linear-gradient(180deg,_#020617,_#111827)]">
        <div class="mx-auto grid min-h-screen max-w-7xl gap-6 px-4 py-6 lg:grid-cols-[260px_1fr]">
            <aside class="rounded-3xl border border-white/10 bg-slate-900/75 p-6 shadow-2xl backdrop-blur">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-cyan-300">{{ auth()->user()->company->name ?? 'Workspace' }}</p>
                    <h1 class="mt-2 text-2xl font-semibold">Smart Inventory</h1>
                    <p class="mt-2 text-sm text-slate-400">Multi-tenant inventory, billing, analytics, and prediction in one Laravel app.</p>
                </div>
                <nav class="mt-8 space-y-2 text-sm">
                    @php($links = [
                        ['route' => 'dashboard', 'label' => 'Dashboard'],
                        ['route' => 'products.index', 'label' => 'Products'],
                        ['route' => 'categories.index', 'label' => 'Categories'],
                        ['route' => 'suppliers.index', 'label' => 'Suppliers'],
                        ['route' => 'warehouses.index', 'label' => 'Warehouses'],
                        ['route' => 'transactions.index', 'label' => 'Transactions'],
                        ['route' => 'billing.index', 'label' => 'Billing'],
                        ['route' => 'company.settings.edit', 'label' => 'Company'],
                        ['route' => 'company.users.index', 'label' => 'Users'],
                        ['route' => 'audit.index', 'label' => 'Audit Logs'],
                        ['route' => 'profile.edit', 'label' => 'Profile'],
                    ])
                    @foreach ($links as $link)
                        @if (Route::has($link['route']))
                            <a href="{{ route($link['route']) }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs($link['route']) ? 'bg-cyan-400 text-slate-950' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">{{ $link['label'] }}</a>
                        @endif
                    @endforeach
                </nav>
                <form method="POST" action="{{ route('logout') }}" class="mt-8">@csrf<button class="w-full rounded-2xl border border-white/10 px-4 py-3 text-left text-sm text-slate-300 transition hover:bg-white/5">Sign out</button></form>
            </aside>
            <main class="space-y-6 rounded-3xl border border-white/10 bg-slate-900/50 p-6 shadow-2xl backdrop-blur">
                @isset($header)
                    <header class="flex flex-col gap-2 border-b border-white/10 pb-6 lg:flex-row lg:items-center lg:justify-between"><div>{{ $header }}</div><div class="text-sm text-slate-400">{{ now()->format('d M Y H:i') }} • {{ auth()->user()->name }}</div></header>
                @endisset
                @if (session('status'))<div class="rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">{{ session('status') }}</div>@endif
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
