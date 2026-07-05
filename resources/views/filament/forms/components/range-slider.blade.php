<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}') }" class="flex items-center space-x-4">
        <input 
            type="range" 
            x-model="state" 
            min="0" 
            max="100" 
            step="1"
            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
        >
        <span class="text-sm font-medium text-gray-900 dark:text-white w-12 text-right" x-text="state + '%'"></span>
    </div>
</x-dynamic-component>
