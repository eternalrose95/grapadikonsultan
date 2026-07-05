@props([
    'title' => 'Article Title',
    'image' => null,
    'date' => null,
    'link' => '#',
    'category' => null,
    'categorySlug' => null,
    'excerpt' => null,
    'readingTime' => null,
    'viewsCount' => null,
])

<article class="group h-full overflow-hidden rounded-2xl border border-border-dark/80 bg-surface-dark/80 shadow-[0_0_0_1px_rgba(255,255,255,0.03)] transition-all duration-300 hover:-translate-y-1 hover:border-primary/40 hover:shadow-2xl">
    <div class="relative aspect-[16/10] overflow-hidden">
        @if($image)
            <img
                alt="{{ $title }}"
                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                src="{{ $image }}"
                loading="lazy"
            >
        @else
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-700 to-gray-800">
                <span class="material-icons-outlined text-5xl text-gray-400">article</span>
            </div>
        @endif

        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>

        @if($category)
            @if($categorySlug)
                <a href="{{ route('blog', ['category' => $categorySlug]) }}" class="absolute left-3 top-3 rounded-full bg-primary/90 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-white transition hover:bg-primary">
                    {{ $category }}
                </a>
            @else
                <span class="absolute left-3 top-3 rounded-full bg-primary/90 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-white">
                    {{ $category }}
                </span>
            @endif
        @endif
    </div>

    <div class="flex flex-col flex-grow p-5 sm:p-6">
        <a href="{{ $link }}" class="block">
            <h3 class="mb-3 line-clamp-2 min-h-[3.2rem] text-lg font-semibold leading-7 text-white transition group-hover:text-primary sm:text-xl">
                {{ $title }}
            </h3>
        </a>

        @if($excerpt)
            <p class="mb-5 flex-grow text-sm leading-6 text-gray-400 line-clamp-3 sm:text-[0.95rem]">
                {{ $excerpt }}
            </p>
        @endif

        <div class="mt-auto border-t border-border-dark/80 pt-4">
            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                @if($date)
                    <span class="flex items-center gap-1.5">
                        <span class="material-icons-outlined text-base text-primary">calendar_today</span>
                        {{ $date }}
                    </span>
                @endif

                @if($readingTime)
                    <span class="flex items-center gap-1.5">
                        <span class="material-icons-outlined text-base text-primary">schedule</span>
                        {{ $readingTime }} min
                    </span>
                @endif

                @if($viewsCount !== null && $viewsCount > 0)
                    <span class="flex items-center gap-1.5">
                        <span class="material-icons-outlined text-base text-primary">visibility</span>
                        {{ number_format($viewsCount) }}
                    </span>
                @endif
            </div>

            <a href="{{ $link }}" class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-primary transition hover:text-primary-400">
                Baca selengkapnya
                <span class="material-icons-outlined text-base">arrow_forward</span>
            </a>
        </div>
    </div>
</article>
