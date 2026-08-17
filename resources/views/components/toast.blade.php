@if (session('success') || session('error') || session('info'))
    @php
        $type = session('success') ? 'success' : (session('error') ? 'error' : 'info');
        $message = session('success') ?? (session('error') ?? session('info'));
        
        $colors = [
            'success' => 'text-status-success bg-status-success/10',
            'error' => 'text-status-danger bg-status-danger/10',
            'info' => 'text-status-info bg-status-info/10'
        ];
        
        $icons = [
            'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>',
            'error' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>',
            'info' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
        ];
    @endphp

    <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms x-init="setTimeout(() => show = false, 4000)" class="fixed bottom-6 right-6 z-50 flex items-center p-4 mb-4 bg-surface border border-border rounded-2xl shadow-xl max-w-sm" role="alert">
        <div class="inline-flex items-center justify-center flex-shrink-0 w-10 h-10 rounded-xl {{ $colors[$type] }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icons[$type] !!}
            </svg>
        </div>
        <div class="ms-3 text-sm font-medium text-primary">{{ $message }}</div>
        <button type="button" @click="show = false" class="ms-auto -mx-1.5 -my-1.5 bg-transparent text-secondary hover:text-primary hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg p-1.5 inline-flex items-center justify-center h-8 w-8 transition-colors">
            <span class="sr-only">Close</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
@endif
