@extends('layouts.app')

@section('title', $title ?? 'Error')

@section('content')
<div class="min-h-[60vh] flex flex-col items-center justify-center text-center px-4 py-20">
    <div class="mb-8">
        <span class="material-icons-outlined text-9xl text-gray-300 dark:text-gray-600">error_outline</span>
    </div>
    
    <h1 class="text-6xl font-bold font-display text-gray-900 dark:text-white mb-4">@yield('code')</h1>
    <h2 class="text-2xl md:text-3xl font-semibold text-gray-700 dark:text-gray-300 mb-6">@yield('message')</h2>
    <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto text-lg">
        @yield('description', 'Maaf, terjadi kesalahan pada permintaan Anda.')
    </p>
    
    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-6 mb-8 shadow-sm border border-gray-200 dark:border-gray-700">
        <p class="text-lg font-medium text-gray-700 dark:text-gray-300">
            Mengalihkan ke beranda dalam <span id="countdown" class="text-primary font-bold text-2xl mx-1">3</span> detik...
        </p>
    </div>

    <a href="{{ route('home') }}" class="bg-primary hover:bg-primary-800 text-white font-bold py-3 px-8 rounded transition shadow-lg inline-flex items-center gap-2">
        <span class="material-icons-outlined">home</span>
        Kembali Sekarang
    </a>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let seconds = 3;
        const countdownElement = document.getElementById('countdown');
        
        const interval = setInterval(() => {
            seconds--;
            if (countdownElement) {
                countdownElement.textContent = seconds;
            }
            
            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = "{{ route('home') }}";
            }
        }, 1000);
    });
</script>
@endpush
@endsection
