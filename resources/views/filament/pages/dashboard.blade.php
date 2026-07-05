<x-filament-panels::page>
    {{-- Tab Navigation --}}
    <div class="mb-6">
        <nav class="flex space-x-1 rounded-xl bg-gray-100 dark:bg-gray-800 p-1" aria-label="Tabs">
            <button
                wire:click="setActiveTab('content')"
                type="button"
                @class([
                    'flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200',
                    'bg-white dark:bg-gray-900 text-primary-600 dark:text-primary-400 shadow-sm' => $activeTab === 'content',
                    'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-700/50' => $activeTab !== 'content',
                ])
            >
                <x-heroicon-o-document-text class="w-5 h-5" />
                <span>Kelola Konten</span>
            </button>

            <button
                wire:click="setActiveTab('crm')"
                type="button"
                @class([
                    'flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200',
                    'bg-white dark:bg-gray-900 text-primary-600 dark:text-primary-400 shadow-sm' => $activeTab === 'crm',
                    'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-700/50' => $activeTab !== 'crm',
                ])
            >
                <x-heroicon-o-chart-bar class="w-5 h-5" />
                <span>Kelola CRM</span>
            </button>
        </nav>
    </div>

    {{-- Dashboard Widgets --}}
    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :widgets="$this->getVisibleWidgets()"
    />
</x-filament-panels::page>
