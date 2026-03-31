<x-app-layout>
    <x-slot name="header"><div><p class="text-sm uppercase tracking-[0.3em] text-cyan-300">Company</p><h2 class="text-3xl font-semibold">Settings</h2></div></x-slot>
    <form method="POST" action="{{ route('company.settings.update') }}" class="grid gap-4 rounded-3xl border border-white/10 bg-white/5 p-6 md:grid-cols-2">
        @csrf @method('PUT')
        <div><x-input-label for="name" value="Company Name" /><x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $company->name)" /></div>
        <div><x-input-label for="email" value="Company Email" /><x-text-input id="email" name="email" class="mt-1 block w-full" :value="old('email', $company->email)" /></div>
        <div><x-input-label for="phone" value="Phone" /><x-text-input id="phone" name="phone" class="mt-1 block w-full" :value="old('phone', $company->phone)" /></div>
        <div><x-input-label for="city" value="City" /><x-text-input id="city" name="city" class="mt-1 block w-full" :value="old('city', $company->city)" /></div>
        <div><x-input-label for="country" value="Country" /><x-text-input id="country" name="country" class="mt-1 block w-full" :value="old('country', $company->country)" /></div>
        <div><x-input-label for="timezone" value="Timezone" /><x-text-input id="timezone" name="timezone" class="mt-1 block w-full" :value="old('timezone', $company->timezone)" /></div>
        <div class="md:col-span-2"><x-input-label for="address" value="Address" /><textarea id="address" name="address" class="mt-1 block w-full rounded-2xl border-slate-700 bg-slate-950/70 text-slate-100">{{ old('address', $company->address) }}</textarea></div>
        <div><x-input-label for="currency" value="Currency" /><x-text-input id="currency" name="currency" class="mt-1 block w-full" :value="old('currency', $company->currency)" /></div>
        <div class="md:col-span-2"><x-primary-button>Save Settings</x-primary-button></div>
    </form>
</x-app-layout>
