@props(['title', 'value', 'trend' => null, 'trendType' => 'up', 'icon' => null])

<div class="bg-surface p-6 rounded-2xl border border-border flex flex-col justify-between hover:shadow-lg transition-shadow duration-300">
    <div class="flex items-center justify-between mb-4">
        <h4 class="text-sm font-medium text-secondary">{{ $title }}</h4>
        @if($icon)
            <div class="w-10 h-10 rounded-full bg-brand-blue/10 flex items-center justify-center text-brand-blue">
                {!! $icon !!}
            </div>
        @endif
    </div>
    
    <div>
        <h2 class="text-3xl font-bold text-primary">{{ $value }}</h2>
        @if($trend)
            <p class="mt-2 text-sm font-medium flex items-center gap-1 {{ $trendType === 'up' ? 'text-status-success' : ($trendType === 'down' ? 'text-status-danger' : 'text-secondary') }}">
                @if($trendType === 'up')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                @elseif($trendType === 'down')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                @endif
                {{ $trend }}
            </p>
        @endif
    </div>
</div>
