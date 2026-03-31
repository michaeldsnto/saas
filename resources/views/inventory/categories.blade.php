<x-app-layout>
    <x-slot name="header"><div><p class="text-sm uppercase tracking-[0.3em] text-cyan-300">Inventory</p><h2 class="text-3xl font-semibold">Categories</h2></div></x-slot>
    <div class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
        <form method="POST" action="{{ route('categories.store') }}" class="space-y-4 rounded-3xl border border-white/10 bg-white/5 p-6">@csrf<div><x-input-label for="name" value="Name" /><x-text-input id="name" name="name" class="mt-1 block w-full" /></div><div><x-input-label for="description" value="Description" /><textarea id="description" name="description" class="mt-1 block w-full rounded-2xl border-slate-700 bg-slate-950/70 text-slate-100"></textarea></div><x-primary-button>Create Category</x-primary-button></form>
        <div class="rounded-3xl border border-white/10 bg-white/5 p-6"><table class="min-w-full text-left text-sm"><thead class="text-slate-400"><tr><th class="pb-3">Name</th><th class="pb-3">Description</th></tr></thead><tbody class="divide-y divide-white/5">@foreach($categories as $category)<tr><td class="py-3">{{ $category->name }}</td><td class="py-3">{{ $category->description }}</td></tr>@endforeach</tbody></table></div>
    </div>
</x-app-layout>
