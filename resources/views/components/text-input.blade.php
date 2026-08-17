@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-border bg-surface text-primary focus:border-brand-blue focus:ring-brand-blue rounded-lg shadow-sm w-full py-2.5 transition duration-150 ease-in-out']) }}>
