@props([
    'author' => null,
])

@if($author)
<div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 flex flex-col sm:flex-row items-center sm:items-start gap-4">
    <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 flex-shrink-0">
        @if($author->avatar ?? false)
        <img src="{{ $author->avatar }}" alt="{{ $author->name }}" class="w-full h-full object-cover">
        @else
        <div class="w-full h-full flex items-center justify-center bg-primary text-white text-2xl font-bold">
            {{ strtoupper(substr($author->name, 0, 1)) }}
        </div>
        @endif
    </div>
    <div class="text-center sm:text-left">
        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-medium">Written by</span>
        <h4 class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $author->name }}</h4>
        @if($author->bio ?? false)
        <p class="text-gray-600 dark:text-gray-400 text-sm mt-2 line-clamp-3">{{ $author->bio }}</p>
        @endif
        @if($author->email ?? false)
        <a href="mailto:{{ $author->email }}" class="inline-flex items-center gap-1 text-primary hover:underline text-sm mt-3">
            <span class="material-icons-outlined text-sm">email</span>
            {{ $author->email }}
        </a>
        @endif
    </div>
</div>
@endif
