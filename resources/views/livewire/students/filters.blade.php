<div class="rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6 shadow-sm">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Filters') }}</h2>
        <button wire:click="toggleFilters" class="px-4 py-2 text-sm font-medium text-neutral-600 dark:text-neutral-300 bg-neutral-100 dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-colors" wire:loading.attr="disabled">
            {{ $showFilters ? __('Hide Filters') : __('Show Filters') }}
        </button>
    </div>
    <div x-show="$wire.showFilters" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="flex flex-col md:flex-row gap-4">
        <div class="relative w-full md:w-64">
            <input
                wire:model.live="search"
                type="text"
                placeholder="{{ __('Search by name, ID, or email...') }}"
                class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
            />
            @if ($search)
                <button
                    wire:click="$set('search', '')"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200"
                    aria-label="{{ __('Clear search') }}"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>
        <select wire:model.live="filters.category" class="w-full md:w-40 p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            <option value="">{{ __('All Categories') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category }}">{{ $category }}</option>
            @endforeach
        </select>
        <select wire:model.live="filters.status" class="w-full md:w-40 p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            <option value="">{{ __('All Statuses') }}</option>
            @foreach ($statuses as $status)
                <option value="{{ $status }}">{{ $status }}</option>
            @endforeach
        </select>
        <select wire:model.live="filters.programme_name" class="w-full md:w-40 p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            <option value="">{{ __('All Programmes') }}</option>
            @foreach ($programmes as $programme)
                <option value="{{ $programme }}">{{ $programme }}</option>
            @endforeach
        </select>
        <select wire:model.live="filters.batch" class="w-full md:w-40 p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            <option value="">{{ __('All Batches') }}</option>
            @foreach ($batches as $batch)
                <option value="{{ $batch }}">{{ $batch }}</option>
            @endforeach
        </select>
        <button wire:click="resetFilters" class="p-3 text-sm font-medium text-neutral-600 dark:text-neutral-300 bg-neutral-100 dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-colors" wire:loading.attr="disabled">{{ __('Reset Filters') }}</button>
    </div>
    <!-- Active Filters -->
    @if ($filters['category'] || $filters['status'] || $filters['programme_name'] || $filters['batch'])
        <div class="flex flex-wrap gap-2 mt-4">
            @if ($filters['category'])
                <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-600 bg-blue-100 dark:bg-blue-900/50 dark:text-blue-300 rounded-full">
                    {{ __('Category') }}: {{ $filters['category'] }}
                    <button wire:click="$set('filters.category', '')" class="ml-2 text-blue-800 dark:text-blue-200 hover:text-blue-900 dark:hover:text-blue-100" aria-label="{{ __('Remove category filter') }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </span>
            @endif
            @if ($filters['status'])
                <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-600 bg-blue-100 dark:bg-blue-900/50 dark:text-blue-300 rounded-full">
                    {{ __('Status') }}: {{ $filters['status'] }}
                    <button wire:click="$set('filters.status', '')" class="ml-2 text-blue-800 dark:text-blue-200 hover:text-blue-900 dark:hover:text-blue-100" aria-label="{{ __('Remove status filter') }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </span>
            @endif
            @if ($filters['programme_name'])
                <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-600 bg-blue-100 dark:bg-blue-900/50 dark:text-blue-300 rounded-full">
                    {{ __('Programme') }}: {{ $filters['programme_name'] }}
                    <button wire:click="$set('filters.programme_name', '')" class="ml-2 text-blue-800 dark:text-blue-200 hover:text-blue-900 dark:hover:text-blue-100" aria-label="{{ __('Remove programme filter') }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </span>
            @endif
            @if ($filters['batch'])
                <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-600 bg-blue-100 dark:bg-blue-900/50 dark:text-blue-300 rounded-full">
                    {{ __('Batch') }}: {{ $filters['batch'] }}
                    <button wire:click="$set('filters.batch', '')" class="ml-2 text-blue-800 dark:text-blue-200 hover:text-blue-900 dark:hover:text-blue-100" aria-label="{{ __('Remove batch filter') }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </span>
            @endif
        </div>
    @endif
</div>