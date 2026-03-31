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
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <div class="flex min-h-screen items-center justify-center bg-[radial-gradient(circle_at_top,_rgba(56,189,248,0.2),_transparent_35%),linear-gradient(180deg,_#020617,_#0f172a)] px-6 py-10">
        <div class="w-full max-w-md rounded-[2rem] border border-white/10 bg-slate-900/80 p-8 shadow-2xl backdrop-blur">
            <div class="mb-8 text-center">
                <a href="/" class="text-xs uppercase tracking-[0.35em] text-cyan-300">Smart Inventory SaaS</a>
                <p class="mt-3 text-sm text-slate-400">Inventory, POS, analytics, and AI stock prediction.</p>
            </div>
            {{ $slot }}
        </div>
    </div>
</body>
</html>
