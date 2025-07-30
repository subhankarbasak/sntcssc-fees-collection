@if (!$student->deleted_at)
    <div x-show="$wire.modalId === 'confirm-deletion-{{ $student->id }}'" x-on:open-modal.window="if ($event.detail.id === 'confirm-deletion-{{ $student->id }}') $wire.set('modalId', 'confirm-deletion-{{ $student->id }}')" x-on:close-modal.window="if ($event.detail.id === 'confirm-deletion-{{ $student->id }}') $wire.set('modalId', null)" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="fixed inset-0 modal-backdrop bg-neutral-900/50 flex items-center justify-center z-50" x-cloak>
        <div class="modal-content bg-white dark:bg-neutral-800 rounded-2xl shadow-xl max-w-lg w-full p-6">
            <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Are you sure you want to delete this student?') }}</h2>
            <p class="text-neutral-600 dark:text-neutral-400 mt-2">{{ __('This action will soft-delete the student record.') }}</p>
            <div class="flex justify-end space-x-3 mt-6">
                <button x-on:click="$wire.set('modalId', null)" class="px-4 py-2 text-neutral-600 dark:text-neutral-300 bg-neutral-100 dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-colors" wire:loading.attr="disabled">{{ __('Cancel') }}</button>
                <button wire:click="delete({{ $student->id }})" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors" wire:loading.attr="disabled">
                    <span wire:loading wire:target="delete({{ $student->id }})" class="animate-spin mr-2">⟳</span>
                    {{ __('Delete') }}
                </button>
            </div>
        </div>
    </div>
@endif