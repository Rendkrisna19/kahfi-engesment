@props([
    'name',
    'id' => null,
    'options' => [], // array of ['value' => '...', 'label' => '...'] or key => value
    'selected' => null,
    'placeholder' => '-- Pilih --',
    'required' => false,
    'disabled' => false,
    'disabledText' => null,
    'onChange' => null,
    'class' => ''
])

@php
    $id = $id ?? $name;
    $selectedValue = old($name, $selected ?? '');
    
    // Normalize options array for Eloquent Collections, Objects, Arrays, & Scalars
    $formattedOptions = [];
    foreach ($options as $key => $val) {
        if (is_object($val)) {
            // Support Eloquent Models & Objects (Campaign, KategoriKonten, KategoriCreator, etc.)
            $value = $val->id ?? $val->value ?? $key;
            $label = $val->nama_campaign ?? $val->nama ?? $val->label ?? $val->name ?? $val->title ?? $value;
            $formattedOptions[] = [
                'value' => (string)$value,
                'label' => (string)$label
            ];
        } elseif (is_array($val)) {
            // Support Arrays
            $value = $val['id'] ?? $val['value'] ?? $key;
            $label = $val['nama_campaign'] ?? $val['nama'] ?? $val['label'] ?? $val['name'] ?? $val['title'] ?? $value;
            $formattedOptions[] = [
                'value' => (string)$value,
                'label' => (string)$label
            ];
        } else {
            // Support Key => Value or Indexed Strings
            $value = is_int($key) ? $val : $key;
            $formattedOptions[] = [
                'value' => (string)$value,
                'label' => (string)$val
            ];
        }
    }

    // Find label for selected value
    $selectedLabel = '';
    foreach ($formattedOptions as $opt) {
        if ((string)$opt['value'] === (string)$selectedValue) {
            $selectedLabel = $opt['label'];
            break;
        }
    }
@endphp

<div x-data="{
    open: false,
    selectedValue: '{{ $selectedValue }}',
    selectedLabel: '{{ addslashes($selectedLabel) }}',
    placeholder: '{{ addslashes($disabled && $disabledText ? $disabledText : $placeholder) }}',
    isDisabled: {{ $disabled ? 'true' : 'false' }},
    options: {{ json_encode($formattedOptions) }},

    selectOption(opt) {
        if (this.isDisabled) return;
        this.selectedValue = opt.value;
        this.selectedLabel = opt.label;
        this.open = false;
        
        $nextTick(() => {
            const input = $refs.hiddenInput;
            input.dispatchEvent(new Event('change', { bubbles: true }));
            @if($onChange)
                {!! $onChange !!};
            @endif
        });
    }
}" @click.outside="open = false" class="relative w-full {{ $class }}">

    <!-- Hidden Native Input for standard Form Submission -->
    <input type="hidden" name="{{ $name }}" id="{{ $id }}" x-ref="hiddenInput" :value="selectedValue" {{ $required ? 'required' : '' }}>

    <!-- Custom Select Trigger Button -->
    <button type="button" 
        @click="if(!isDisabled) open = !open" 
        :disabled="isDisabled"
        class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 text-xs font-semibold rounded-xl bg-body border border-border text-primary hover:border-brand-blue/50 focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition-all duration-200 shadow-2xs cursor-pointer disabled:cursor-not-allowed disabled:opacity-60 disabled:bg-gray-100 dark:disabled:bg-gray-800">
        
        <span class="truncate" :class="{'text-secondary font-normal': !selectedValue && selectedValue !== 0, 'text-primary font-semibold': selectedValue || selectedValue === 0}" x-text="selectedLabel || placeholder">
        </span>

        <svg class="w-4 h-4 text-secondary shrink-0 transition-transform duration-200" :class="{'rotate-180 text-brand-blue': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <!-- Custom Dropdown Menu -->
    <div x-show="open" 
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
        style="display: none;" 
        class="absolute z-50 w-full mt-1.5 bg-surface border border-border rounded-xl shadow-xl overflow-hidden py-1 max-h-60 overflow-y-auto">
        
        <!-- Placeholder / Reset Option -->
        @if(!$required)
            <div @click="selectOption({value: '', label: '{{ addslashes($placeholder) }}'})" 
                class="px-3.5 py-2 text-xs text-secondary hover:bg-gray-100 dark:hover:bg-gray-800/80 cursor-pointer flex items-center justify-between transition-colors">
                <span>{{ $placeholder }}</span>
                <template x-if="!selectedValue">
                    <svg class="w-3.5 h-3.5 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </template>
            </div>
        @endif

        <!-- Option Items -->
        <template x-for="opt in options" :key="opt.value">
            <div @click="selectOption(opt)" 
                class="px-3.5 py-2.5 text-xs text-primary hover:bg-brand-blue/10 hover:text-brand-blue cursor-pointer flex items-center justify-between transition-colors group"
                :class="{'bg-brand-blue/10 text-brand-blue font-bold': String(selectedValue) === String(opt.value)}">
                
                <span class="truncate" x-text="opt.label"></span>
                
                <template x-if="String(selectedValue) === String(opt.value)">
                    <svg class="w-3.5 h-3.5 text-brand-blue shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </template>
            </div>
        </template>

        <template x-if="options.length === 0">
            <div class="px-3.5 py-3 text-xs text-secondary text-center">
                {{ $disabledText ?? 'Tidak ada pilihan tersedia' }}
            </div>
        </template>
    </div>
</div>
