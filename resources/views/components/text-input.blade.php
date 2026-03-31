@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-md border-gray-300 bg-white text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-indigo-500 focus:ring-indigo-500']) }}>
