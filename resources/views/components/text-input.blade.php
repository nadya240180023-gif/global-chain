@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-slate-50 text-slate-900 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm']) }}>
