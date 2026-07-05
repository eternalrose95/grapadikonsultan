@props([
    'quote' => 'We combine rigorous data analysis with creative strategic thinking to deliver results that matter.',
    'backgroundImage' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1200',
    'height' => '400px',
])

@php
    // Handle image - check if it's a URL or storage path
    $imageUrl = $backgroundImage;
    if ($backgroundImage && !str_starts_with($backgroundImage, 'http')) {
        $imageUrl = asset('storage/' . $backgroundImage);
    }
@endphp

<section 
    class="relative w-full flex items-center justify-center overflow-hidden" 
    style="height: {{ $height }};"
>
    {{-- Background Image --}}
    <img 
        src="{{ $imageUrl }}" 
        alt="Quote Background" 
        class="absolute inset-0 w-full h-full object-cover"
        loading="lazy"
    >
    <div class="absolute inset-0 bg-navy-brand/80"></div>
    <div class="relative z-10 max-w-4xl px-6 text-center">
        <h2 class="text-white text-2xl md:text-4xl font-bold mb-6">
            "{{ $quote }}"
        </h2>
        <div class="h-1 w-20 bg-primary mx-auto"></div>
    </div>
</section>
