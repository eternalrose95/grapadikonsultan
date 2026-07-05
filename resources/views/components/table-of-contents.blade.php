@props([
    'content' => '',
])

@php
// Extract headings from HTML content
preg_match_all('/<h([2-3])[^>]*>(.*?)<\/h[2-3]>/i', $content, $matches, PREG_SET_ORDER);

$headings = collect($matches)->map(function ($match, $index) {
    $level = $match[1];
    $text = strip_tags($match[2]);
    $slug = \Illuminate\Support\Str::slug($text) . '-' . $index;
    return [
        'level' => $level,
        'text' => $text,
        'slug' => $slug,
    ];
});
@endphp

@if($headings->count() > 2)
<div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6" x-data="{ open: true }">
    <button @click="open = !open" class="w-full flex items-center justify-between text-left">
        <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <span class="material-icons-outlined text-primary">list</span>
            Table of Contents
        </h3>
        <span class="material-icons-outlined text-gray-500 transition-transform" :class="{ 'rotate-180': !open }">
            expand_more
        </span>
    </button>
    <nav x-show="open" x-collapse class="mt-4">
        <ul class="space-y-2">
            @foreach($headings as $heading)
            <li class="{{ $heading['level'] == 3 ? 'pl-4' : '' }}">
                <a href="#{{ $heading['slug'] }}" 
                   class="block py-1 text-sm text-gray-600 dark:text-gray-400 hover:text-primary transition {{ $heading['level'] == 2 ? 'font-medium' : '' }}">
                    {{ $heading['text'] }}
                </a>
            </li>
            @endforeach
        </ul>
    </nav>
</div>
@endif
