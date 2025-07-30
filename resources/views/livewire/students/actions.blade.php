<div class="rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6 shadow-sm">
    <div class="flex flex-col md:flex-row gap-3 justify-between items-center">
        <div class="flex gap-3 flex-wrap">
            <div x-data="{ tooltip: false }" class="relative">
                <button wire:click="openModal" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors" wire:loading.attr="disabled" x-on:mouseover="tooltip = true" x-on:mouseout="tooltip = false">
                    {{ __('Add Student') }}
                </button>
                <span x-show="tooltip" class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-2 py-1 text-xs text-white bg-neutral-800 rounded shadow-lg z-[60]">{{ __('Create a new student record') }}</span>
            </div>
            <div x-data="{ tooltip: false }" class="relative">
                <button wire:click="export(false)" class="px-4 py-2 text-neutral-600 dark:text-neutral-300 bg-neutral-100 dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-colors" wire:loading.attr="disabled" x-on:mouseover="tooltip = true" x-on:mouseout="tooltip = false">
                    <span wire:loading wire:target="export(false)" class="animate-spin mr-2">⟳</span>
                    {{ $exporting ? __('Exporting...') : __('Export All') }}
                </button>
                <span x-show="tooltip" class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-2 py-1 text-xs text-white bg-neutral-800 rounded shadow-lg z-[60]">{{ __('Export all students to CSV') }}</span>
            </div>
            @if (!empty($selectedStudents))
                <div x-data="{ tooltip: false }" class="relative">
                    <button wire:click="export(true)" class="px-4 py-2 text-neutral-600 dark:text-neutral-300 bg-neutral-100 dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-colors" wire:loading.attr="disabled" x-on:mouseover="tooltip = true" x-on:mouseout="tooltip = false">
                        <span wire:loading wire:target="export(true)" class="animate-spin mr-2">⟳</span>
                        {{ $exporting ? __('Exporting...') : __('Export Selected') }}
                    </button>
                    <span x-show="tooltip" class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-2 py-1 text-xs text-white bg-neutral-800 rounded shadow-lg z-[60]">{{ __('Export selected students to CSV') }}</span>
                </div>
                <div x-data="{ tooltip: false }" class="relative">
                    <button x-on:click="$wire.set('modalId', 'confirm-bulk-deletion')" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors" wire:loading.attr="disabled" x-on:mouseover="tooltip = true" x-on:mouseout="tooltip = false">
                        {{ __('Delete Selected (') . count($selectedStudents) . __(')') }}
                    </button>
                    <span x-show="tooltip" class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-2 py-1 text-xs text-white bg-neutral-800 rounded shadow-lg z-[60]">{{ __('Soft-delete selected students') }}</span>
                </div>
                @if ($showDeleted)
                    <div x-data="{ tooltip: false }" class="relative">
                        <button x-on:click="$wire.set('modalId', 'confirm-bulk-restore')" class="px-4 py-2 text-neutral-600 dark:text-neutral-300 bg-neutral-100 dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-colors" wire:loading.attr="disabled" x-on:mouseover="tooltip = true" x-on:mouseout="tooltip = false">
                            {{ __('Restore Selected (') . count($selectedStudents) . __(')') }}
                        </button>
                        <span x-show="tooltip" class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-2 py-1 text-xs text-white bg-neutral-800 rounded shadow-lg z-[60]">{{ __('Restore selected students') }}</span>
                    </div>
                @endif
            @endif
        </div>
        <label class="flex items-center gap-2">
            <input wire:model.live="showDeleted" type="checkbox" class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-neutral-300 dark:border-neutral-600 rounded" />
            <span class="text-neutral-600 dark:text-neutral-300">{{ __('Show Deleted') }}</span>
        </label>
    </div>
</div>