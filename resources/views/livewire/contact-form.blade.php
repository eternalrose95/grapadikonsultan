<div>
    @if($submitted)
        {{-- Success Message --}}
        <div class="text-center py-12">
            <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Terima Kasih!</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Pesan Anda telah terkirim. Tim kami akan segera menghubungi Anda.</p>
            <button 
                wire:click="resetForm"
                class="inline-flex items-center gap-2 bg-primary hover:bg-primary-800 text-white font-bold py-3 px-6 rounded-lg transition"
            >
                <span class="material-icons-outlined">refresh</span>
                Kirim Pesan Lain
            </button>
        </div>
    @else
        {{-- Contact Form --}}
        <form wire:submit="submit" class="space-y-6">
            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="name"
                    wire:model="name"
                    placeholder="Masukkan nama lengkap Anda"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- WhatsApp --}}
            <div>
                <label for="whatsapp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nomor WhatsApp <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </span>
                    <input 
                        type="tel" 
                        id="whatsapp"
                        wire:model="whatsapp"
                        placeholder="08xxxxxxxxxx"
                        class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                    >
                </div>
                @error('whatsapp')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Company --}}
            <div>
                <label for="company" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nama Perusahaan <span class="text-gray-400 text-xs">(opsional)</span>
                </label>
                <input 
                    type="text" 
                    id="company"
                    wire:model="company"
                    placeholder="Nama perusahaan Anda"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition"
                >
            </div>

            {{-- Message --}}
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Pesan <span class="text-gray-400 text-xs">(opsional)</span>
                </label>
                <textarea 
                    id="message"
                    wire:model="message"
                    rows="4"
                    placeholder="Ceritakan kebutuhan Anda..."
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition resize-none"
                ></textarea>
            </div>

            {{-- Submit Button --}}
            <button 
                type="submit"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-75 cursor-wait"
                class="w-full bg-primary hover:bg-primary-800 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-3"
            >
                <span wire:loading.remove>
                    <span class="material-icons-outlined">send</span>
                </span>
                <span wire:loading>
                    <svg class="animate-spin h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                <span wire:loading.remove>Kirim Pesan</span>
                <span wire:loading>Mengirim...</span>
            </button>

            <p class="text-center text-xs text-gray-500 dark:text-gray-400">
                Dengan mengirim formulir ini, Anda menyetujui kami untuk menghubungi Anda.
            </p>
        </form>
    @endif
</div>
