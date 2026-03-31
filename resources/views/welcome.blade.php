<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Smart Inventory SaaS</title>
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <div class="mx-auto flex min-h-screen max-w-6xl items-center px-6 py-12">
        <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
            <div>
                <p class="text-sm uppercase tracking-[0.35em] text-cyan-300">Laravel SaaS Inventory</p>
                <h1 class="mt-4 text-5xl font-semibold leading-tight text-white">Real-time stock control, POS, subscriptions, analytics, and AI restock insight.</h1>
                <p class="mt-6 max-w-2xl text-lg text-slate-300">Production-minded starter for a smart inventory SaaS with tenant isolation, audit trails, realtime updates, REST APIs, and plan-based feature limits.</p>
                <div class="mt-8 flex gap-4">
                    <a href="{{ route('register') }}" class="rounded-2xl bg-cyan-400 px-6 py-3 font-medium text-slate-950">Start Free</a>
                    <a href="{{ route('login') }}" class="rounded-2xl border border-white/10 px-6 py-3 text-slate-100">Sign In</a>
                </div>
            </div>
            <div class="rounded-[2rem] border border-white/10 bg-slate-900/70 p-8 shadow-2xl">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-white/5 p-4"><p class="text-sm text-slate-400">Architecture</p><p class="mt-2 text-xl font-semibold">Multi-tenant</p></div>
                    <div class="rounded-2xl bg-white/5 p-4"><p class="text-sm text-slate-400">Realtime</p><p class="mt-2 text-xl font-semibold">Broadcast events</p></div>
                    <div class="rounded-2xl bg-white/5 p-4"><p class="text-sm text-slate-400">Analytics</p><p class="mt-2 text-xl font-semibold">Revenue + trends</p></div>
                    <div class="rounded-2xl bg-white/5 p-4"><p class="text-sm text-slate-400">Smart feature</p><p class="mt-2 text-xl font-semibold">Stock prediction</p></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
