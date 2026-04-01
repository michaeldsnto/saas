<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs uppercase tracking-[0.28em] text-cyan-300 sm:text-sm sm:tracking-[0.3em]">Inventory</p>
            <h2 class="text-2xl font-semibold sm:text-3xl">Products</h2>
        </div>
    </x-slot>

    <div class="grid gap-4 xl:grid-cols-[0.95fr_1.05fr] xl:gap-6">
        <div class="space-y-4 sm:space-y-6">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h3 class="text-xl font-semibold">Create Product</h3>
                            <span class="rounded-full bg-cyan-300 px-3 py-1 text-xs font-semibold text-slate-950">{{ $currentPlan?->name ?? 'No Plan' }}</span>
                        </div>
                        <p class="mt-2 text-sm text-slate-400">Product creation follows your active plan limit. If the limit is reached, the backend will block new product creation.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('products.store') }}" class="mt-6 grid gap-4">
                    @csrf
                    <div><x-input-label for="name" value="Name" /><x-text-input id="name" name="name" class="mt-1 block w-full" /></div>
                    <div><x-input-label for="sku" value="SKU" /><x-text-input id="sku" name="sku" class="mt-1 block w-full" /></div>
                    <div><x-input-label for="category_id" value="Category" /><select id="category_id" name="category_id" class="mt-1 block w-full rounded-2xl border-slate-700 bg-slate-950/70 text-slate-100"><option value="">Select category</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></div>
                    <div><x-input-label for="supplier_id" value="Supplier" /><select id="supplier_id" name="supplier_id" class="mt-1 block w-full rounded-2xl border-slate-700 bg-slate-950/70 text-slate-100"><option value="">Select supplier</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}">{{ $supplier->name }}</option>@endforeach</select></div>
                    <div class="grid gap-4 md:grid-cols-2"><div><x-input-label for="price" value="Price" /><x-text-input id="price" name="price" type="number" step="0.01" class="mt-1 block w-full" /></div><div><x-input-label for="cost_price" value="Cost Price" /><x-text-input id="cost_price" name="cost_price" type="number" step="0.01" class="mt-1 block w-full" /></div></div>
                    <div class="grid gap-4 md:grid-cols-2"><div><x-input-label for="minimum_stock" value="Minimum Stock" /><x-text-input id="minimum_stock" name="minimum_stock" type="number" class="mt-1 block w-full" value="0" /></div><div><x-input-label for="opening_stock" value="Opening Stock" /><x-text-input id="opening_stock" name="opening_stock" type="number" class="mt-1 block w-full" value="0" /></div></div>
                    <div><x-input-label for="warehouse_id" value="Warehouse" /><select id="warehouse_id" name="warehouse_id" class="mt-1 block w-full rounded-2xl border-slate-700 bg-slate-950/70 text-slate-100"><option value="">Select warehouse</option>@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>@endforeach</select></div>
                    <div><x-input-label for="description" value="Description" /><textarea id="description" name="description" class="mt-1 block min-h-28 w-full rounded-2xl border-slate-700 bg-slate-950/70 text-slate-100"></textarea></div>
                    <x-primary-button class="justify-center sm:justify-start">Create Product</x-primary-button>
                </form>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-xl font-semibold">Import & Export</h3>
                        <p class="mt-2 text-sm text-slate-400">This area is unlocked on Pro and Enterprise.</p>
                    </div>
                    @if($canUseImportExport)
                        <span class="w-fit rounded-full bg-emerald-300 px-3 py-1 text-xs font-semibold text-slate-950">Feature unlocked</span>
                    @else
                        <span class="w-fit rounded-full bg-amber-300 px-3 py-1 text-xs font-semibold text-slate-950">Upgrade to Pro</span>
                    @endif
                </div>

                @if($canUseImportExport)
                    <form method="POST" enctype="multipart/form-data" action="{{ route('products.import') }}" class="mt-6 space-y-4">
                        @csrf
                        <div class="grid gap-4 md:grid-cols-2">
                            <div><x-input-label for="file" value="Import Excel/CSV" /><input id="file" type="file" name="file" class="mt-1 block w-full text-sm"></div>
                            <div><x-input-label for="import_warehouse_id" value="Warehouse" /><select id="import_warehouse_id" name="warehouse_id" class="mt-1 block w-full rounded-2xl border-slate-700 bg-slate-950/70 text-slate-100">@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>@endforeach</select></div>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                            <x-primary-button class="justify-center sm:justify-start">Import Products</x-primary-button>
                            <a href="{{ route('products.export.excel') }}" class="rounded-2xl border border-white/10 px-4 py-2 text-center">Excel Export</a>
                            <a href="{{ route('products.export.pdf') }}" class="rounded-2xl border border-white/10 px-4 py-2 text-center">PDF Export</a>
                        </div>
                    </form>
                @else
                    <div class="mt-6 rounded-2xl border border-amber-400/20 bg-amber-400/10 p-4 text-sm text-amber-100">Import and export tools are disabled on the Free plan. Upgrade to Pro or Enterprise to use Excel import and PDF/Excel exports.</div>
                @endif
            </div>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 sm:p-6">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-xl font-semibold">Product List</h3>
                    <p class="mt-1 text-sm text-slate-400">Stock, pricing, and prediction in one place.</p>
                </div>
                @if($canUsePrediction)
                    <span class="w-fit rounded-full bg-emerald-300 px-3 py-1 text-xs font-semibold text-slate-950">Prediction unlocked</span>
                @else
                    <span class="w-fit rounded-full bg-amber-300 px-3 py-1 text-xs font-semibold text-slate-950">Prediction locked on Free</span>
                @endif
            </div>

            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-full text-left text-sm">
                    <thead class="text-slate-400"><tr><th class="pb-3">Product</th><th class="pb-3">Stock</th><th class="pb-3">Price</th><th class="pb-3">Prediction</th></tr></thead>
                    <tbody class="divide-y divide-white/5">@foreach($products as $product)<tr><td class="py-3"><div class="font-medium">{{ $product->name }}</div><div class="text-xs text-slate-400">{{ $product->sku }}</div></td><td class="py-3">{{ $product->stocks->sum('quantity') }}</td><td class="py-3">Rp {{ number_format($product->price, 0, ',', '.') }}</td><td class="py-3 text-slate-300">@if($canUsePrediction){{ $predictions[$product->id]['estimated_run_out_date'] ?? 'Not enough data' }}@else Upgrade to Pro @endif</td></tr>@endforeach</tbody>
                </table>
            </div>

            <div class="mobile-card-grid md:hidden">
                @forelse($products as $product)
                    <article class="mobile-data-card">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="font-semibold">{{ $product->name }}</h4>
                                <p class="mt-1 text-xs text-slate-400">{{ $product->sku }}</p>
                            </div>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs text-slate-200">{{ $product->stocks->sum('quantity') }} pcs</span>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-slate-500">Price</p>
                                <p class="mt-1 font-medium">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-slate-500">Prediction</p>
                                <p class="mt-1 text-slate-300">@if($canUsePrediction){{ $predictions[$product->id]['estimated_run_out_date'] ?? 'Not enough data' }}@else Upgrade to Pro @endif</p>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="mobile-data-card text-sm text-slate-400">No products found yet.</div>
                @endforelse
            </div>

            <div class="mt-4">{{ $products->links() }}</div>
        </div>
    </div>
</x-app-layout>