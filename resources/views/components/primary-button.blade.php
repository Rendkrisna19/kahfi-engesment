<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-brand-gradient border border-transparent rounded-lg font-bold text-sm text-white tracking-widest hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2 transition-all ease-in-out duration-200']) }}>
    {{ $slot }}
</button>
