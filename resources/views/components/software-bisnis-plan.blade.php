@props([
    'tagline' => 'Software Bisnis Plan: Mudah, Murah, dan Cepat',
    'title' => 'Susun Rencana Bisnis',
    'highlightedTitle' => 'Lebih Cerdas',
    'description' => 'Platform all-in-one untuk penyusunan strategi, proyeksi keuangan otomatis (NPV, IRR, Payback Period), dan analisis SWOT berbasis AI.',
    'callout' => 'Semua dalam satu solusi terintegrasi.',
    'primaryButtonText' => 'MULAI SEKARANG GRATIS',
    'primaryButtonUrl' => '/contact',
    'secondaryButtonText' => 'LIHAT FITUR',
    'secondaryButtonUrl' => '/services',
    'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800',
    'imageAlt' => 'Dashboard Preview',
    'showLaptopFrame' => true,
])

@php
    // Handle image - check if it's a URL or storage path
    $imageUrl = $image;
    if ($image && !str_starts_with($image, 'http')) {
        $imageUrl = asset('storage/' . $image);
    }
@endphp

<section class="py-20 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            {{-- Text Content --}}
            <div class="text-center lg:text-left">
                @if($tagline)
                <p class="text-gray-600 dark:text-gray-400 mb-2 font-medium text-lg">
                    {{ $tagline }}
                </p>
                @endif
                
                @if($title)
                <h2 class="text-4xl md:text-6xl font-bold font-display text-gray-900 dark:text-white mb-2">
                    {{ $title }}
                </h2>
                @endif
                
                @if($highlightedTitle)
                <h2 class="text-4xl md:text-6xl font-bold font-display text-primary mb-6">
                    {{ $highlightedTitle }}
                </h2>
                @endif
                
                @if($description)
                <p class="text-gray-600 dark:text-gray-400 mb-4 max-w-lg mx-auto lg:mx-0 text-xl lg:text-2xl">
                    {{ $description }}
                </p>
                @endif
                
                @if($callout)
                <p class="text-gray-800 dark:text-gray-200 font-semibold mb-8 text-lg">
                    {{ $callout }}
                </p>
                @endif
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    @if($primaryButtonText)
                    <a href="{{ $primaryButtonUrl }}" class="bg-primary hover:bg-primary-800 text-white font-bold py-4 px-10 rounded transition shadow-lg inline-flex items-center justify-center gap-2 text-lg">
                        {{ $primaryButtonText }}
                    </a>
                    @endif
                    
                    @if($secondaryButtonText)
                    <a href="{{ $secondaryButtonUrl }}" class="border-2 border-primary text-primary hover:bg-primary hover:text-white font-bold py-4 px-10 rounded transition inline-flex items-center justify-center gap-2 text-lg">
                        {{ $secondaryButtonText }}
                    </a>
                    @endif
                </div>
            </div>
            
            {{-- Image/Laptop Mockup --}}
            <div class="relative">
                <div class="relative mx-auto max-w-lg lg:max-w-none">
                    @if($showLaptopFrame)
                    {{-- Laptop Frame --}}
                    <div class="relative bg-gray-800 rounded-t-xl pt-4 px-4 pb-0 shadow-2xl">
                        {{-- Screen --}}
                        <div class="bg-gray-900 rounded-t-lg overflow-hidden aspect-video">
                            <img src="{{ $imageUrl }}" alt="{{ $imageAlt }}" class="w-full h-full object-cover" loading="lazy">
                        </div>
                    </div>
                    {{-- Laptop Base --}}
                    <div class="bg-gray-700 h-4 rounded-b-xl relative">
                        <div class="absolute left-1/2 -translate-x-1/2 top-0 w-20 h-1 bg-gray-600 rounded-b"></div>
                    </div>
                    {{-- Shadow --}}
                    <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 w-3/4 h-4 bg-black/20 blur-xl rounded-full"></div>
                    @else
                    {{-- Simple Image --}}
                    <img src="{{ $imageUrl }}" alt="{{ $imageAlt }}" class="w-full h-auto rounded-lg shadow-2xl" loading="lazy">
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
