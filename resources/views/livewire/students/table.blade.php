<div class="relative h-full flex-1 overflow-hidden rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6 shadow-sm" x-data="{ selectAll: false }" x-init="selectAll = $wire.selectedStudents.length === {{ $students->count() }} && $wire.selectedStudents.length > 0">
    @if ($students->isEmpty())
        <div class="flex flex-col items-center justify-center h-full">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            <p class="text-center text-neutral-500 dark:text-neutral-400 text-lg">{{ __('No students found.') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="sticky top-0 bg-neutral-100 dark:bg-neutral-800 z-10">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-medium text-neutral-500 dark:text-neutral-300 uppercase">
                            <input
                                type="checkbox"
                                class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-neutral-300 dark:border-neutral-600 rounded"
                                x-model="selectAll"
                                x-on:change="selectAll ? $wire.set('selectedStudents', {{ json_encode($students->pluck('id')->toArray()) }}) : $wire.set('selectedStudents', [])"
                                wire:loading.attr="disabled"
                            />
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-neutral-500 dark:text-neutral-300 uppercase cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors" wire:click="sortBy('student_id')">
                            {{ __('Student ID') }} {{ $sortField === 'student_id' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-neutral-500 dark:text-neutral-300 uppercase cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors" wire:click="sortBy('first_name')">
                            {{ __('Name') }} {{ $sortField === 'first_name' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-neutral-500 dark:text-neutral-300 uppercase">{{ __('Programme') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-neutral-500 dark:text-neutral-300 uppercase">{{ __('Email') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-neutral-500 dark:text-neutral-300 uppercase">{{ __('Category') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-neutral-500 dark:text-neutral-300 uppercase">{{ __('Status') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-medium text-neutral-500 dark:text-neutral-300 uppercase">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @foreach ($students as $student)
                        <tr class="{{ $student->deleted_at ? 'bg-red-50 dark:bg-red-900/20' : 'hover:bg-neutral-50 dark:hover:bg-neutral-700' }} transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input
                                    wire:model.live="selectedStudents"
                                    value="{{ $student->id }}"
                                    type="checkbox"
                                    class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-neutral-300 dark:border-neutral-600 rounded"
                                    wire:loading.attr="disabled"
                                    x-on:change="$wire.selectedStudents.length === {{ $students->count() }} ? selectAll = true : selectAll = false"
                                />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $student->student_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $student->first_name }} {{ $student->last_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $student->programme_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($quickEdit['id'] === $student->id)
                                    <input
                                        wire:model.debounce.300ms="quickEdit.email"
                                        type="email"
                                        class="w-full p-2 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                        wire:loading.attr="disabled"
                                        x-on:focus="$dispatch('focus-quick-edit', { id: {{ $student->id }} })"
                                    />
                                    @error('quickEdit.email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                                @else
                                    {{ $student->email }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $student->category }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($quickEdit['id'] === $student->id)
                                    <input
                                        wire:model.live="quickEdit.status"
                                        type="text"
                                        class="w-full p-2 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                        wire:loading.attr="disabled"
                                    />
                                    @error('quickEdit.status') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                                @else
                                    {{ $student->status }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                @if ($quickEdit['id'] === $student->id)
                                    <div x-data="{ tooltip: false }" class="relative">
                                        <button
                                            wire:click="saveQuickEdit"
                                            class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm"
                                            wire:loading.attr="disabled"
                                            wire:target="saveQuickEdit"
                                            x-on:mouseover="tooltip = true"
                                            x-on:mouseout="tooltip = false"
                                        >
                                            <span wire:loading wire:target="saveQuickEdit" class="animate-spin inline-block w-4 h-4 mr-2">⟳</span>
                                            {{ __('Save') }}
                                        </button>
                                        <span x-show="tooltip" class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-2 py-1 text-xs text-white bg-neutral-800 rounded shadow-lg z-[60]">{{ __('Save quick edit changes') }}</span>
                                    </div>
                                    <div x-data="{ tooltip: false }" class="relative">
                                        <button
                                            wire:click="$set('quickEdit.id', null)"
                                            class="px-3 py-1 text-neutral-600 dark:text-neutral-300 bg-neutral-100 dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-colors text-sm"
                                            wire:loading.attr="disabled"
                                            wire:target="$set('quickEdit.id', null)"
                                            x-on:mouseover="tooltip = true"
                                            x-on:mouseout="tooltip = false"
                                        >
                                            <span wire:loading wire:target="$set('quickEdit.id', null)" class="animate-spin inline-block w-4 h-4 mr-2">⟳</span>
                                            {{ __('Cancel') }}
                                        </button>
                                        <span x-show="tooltip" class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-2 py-1 text-xs text-white bg-neutral-800 rounded shadow-lg z-[60]">{{ __('Cancel quick edit') }}</span>
                                    </div>
                                @else
                                    <div x-data="{ tooltip: false }" class="relative">
                                        <button
                                            wire:click="openQuickEdit({{ $student->id }})"
                                            class="px-3 py-1 text-neutral-600 dark:text-neutral-300 bg-neutral-100 dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-colors text-sm"
                                            aria-label="{{ __('Quick edit student') }}"
                                            wire:loading.attr="disabled"
                                            wire:target="openQuickEdit({{ $student->id }})"
                                            x-on:mouseover="tooltip = true"
                                            x-on:mouseout="tooltip = false"
                                        >
                                            <span wire:loading wire:target="openQuickEdit({{ $student->id }})" class="animate-spin inline-block w-4 h-4 mr-2">⟳</span>
                                            {{ __('Quick Edit') }}
                                        </button>
                                        <span x-show="tooltip" class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-2 py-1 text-xs text-white bg-neutral-800 rounded shadow-lg z-[60]">{{ __('Quickly edit email and status') }}</span>
                                    </div>
                                    <div x-data="{ tooltip: false }" class="relative">
                                        <button
                                            wire:click="openModal({{ $student->id }})"
                                            class="px-3 py-1 text-neutral-600 dark:text-neutral-300 bg-neutral-100 dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-colors text-sm"
                                            aria-label="{{ __('Edit student') }}"
                                            wire:loading.attr="disabled"
                                            wire:target="openModal({{ $student->id }})"
                                            x-on:mouseover="tooltip = true"
                                            x-on:mouseout="tooltip = false"
                                        >
                                            <span wire:loading wire:target="openModal({{ $student->id }})" class="animate-spin inline-block w-4 h-4 mr-2">⟳</span>
                                            {{ __('Edit') }}
                                        </button>
                                        <span x-show="tooltip" class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-2 py-1 text-xs text-white bg-neutral-800 rounded shadow-lg z-[60]">{{ __('Edit full student details') }}</span>
                                    </div>
                                    @if ($student->deleted_at)
                                        <div x-data="{ tooltip: false }" class="relative">
                                            <button
                                                x-on:click="$wire.set('modalId', 'confirm-restore-{{ $student->id }}')"
                                                class="px-3 py-1 text-neutral-600 dark:text-neutral-300 bg-neutral-100 dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-colors text-sm"
                                                aria-label="{{ __('Restore student') }}"
                                                wire:loading.attr="disabled"
                                                wire:target="$set('modalId', 'confirm-restore-{{ $student->id }}')"
                                                x-on:mouseover="tooltip = true"
                                                x-on:mouseout="tooltip = false"
                                            >
                                                <span wire:loading wire:target="$set('modalId', 'confirm-restore-{{ $student->id }}')" class="animate-spin inline-block w-4 h-4 mr-2">⟳</span>
                                                {{ __('Restore') }}
                                            </button>
                                            <span x-show="tooltip" class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-2 py-1 text-xs text-white bg-neutral-800 rounded shadow-lg z-[60]">{{ __('Restore deleted student') }}</span>
                                        </div>
                                    @else
                                        <div x-data="{ tooltip: false }" class="relative">
                                            <button
                                                x-on:click="$wire.set('modalId', 'confirm-deletion-{{ $student->id }}')"
                                                class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm"
                                                aria-label="{{ __('Delete student') }}"
                                                wire:loading.attr="disabled"
                                                wire:target="$set('modalId', 'confirm-deletion-{{ $student->id }}')"
                                                x-on:mouseover="tooltip = true"
                                                x-on:mouseout="tooltip = false"
                                            >
                                                <span wire:loading wire:target="$set('modalId', 'confirm-deletion-{{ $student->id }}')" class="animate-spin inline-block w-4 h-4 mr-2">⟳</span>
                                                {{ __('Delete') }}
                                            </button>
                                            <span x-show="tooltip" class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-2 py-1 text-xs text-white bg-neutral-800 rounded shadow-lg z-[60]">{{ __('Soft-delete this student') }}</span>
                                        </div>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @include('livewire.students.modals.delete-modal', ['student' => $student])
                        @include('livewire.students.modals.restore-modal', ['student' => $student])
                    @endforeach
                </tbody>
            </table>
            <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4">
                <select wire:model.live="perPage" class="w-32 p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    <option value="10">{{ __('10 per page') }}</option>
                    <option value="25">{{ __('25 per page') }}</option>
                    <option value="50">{{ __('50 per page') }}</option>
                </select>
                <div class="flex items-center gap-3">
                    <input
                        wire:model.live="gotoPage"
                        type="number"
                        min="1"
                        max="{{ $students->lastPage() }}"
                        class="w-24 p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    {{ $students->links() }}
                </div>
            </div>
        </div>
    @endif
    <!-- Sticky Actions Bar -->
    <div
        x-show="$wire.selectedStudents.length > 0"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-[60] bg-white dark:bg-neutral-800 rounded-2xl border border-neutral-200 dark:border-neutral-700 p-4 flex gap-3 shadow-lg"
    >
        <p class="text-neutral-600 dark:text-neutral-300 font-medium">{{ count($selectedStudents) }} {{ __('selected') }}</p>
        <div x-data="{ tooltip: false }" class="relative">
            <button
                wire:click="export(true)"
                class="px-4 py-2 text-neutral-600 dark:text-neutral-300 bg-neutral-100 dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-colors"
                wire:loading.attr="disabled"
                wire:target="export(true)"
                x-on:mouseover="tooltip = true"
                x-on:mouseout="tooltip = false"
            >
                <span wire:loading wire:target="export(true)" class="animate-spin inline-block w-4 h-4 mr-2">⟳</span>
                {{ $exporting ? __('Exporting...') : __('Export Selected') }}
            </button>
            <span x-show="tooltip" class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-2 py-1 text-xs text-white bg-neutral-800 rounded shadow-lg z-[60]">{{ __('Export selected students to CSV') }}</span>
        </div>
        <div x-data="{ tooltip: false }" class="relative">
            <button
                x-on:click="$wire.set('modalId', 'confirm-bulk-deletion')"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                wire:loading.attr="disabled"
                wire:target="$set('modalId', 'confirm-bulk-deletion')"
                x-on:mouseover="tooltip = true"
                x-on:mouseout="tooltip = false"
            >
                <span wire:loading wire:target="$set('modalId', 'confirm-bulk-deletion')" class="animate-spin inline-block w-4 h-4 mr-2">⟳</span>
                {{ __('Delete Selected') }}
            </button>
            <span x-show="tooltip" class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-2 py-1 text-xs text-white bg-neutral-800 rounded shadow-lg z-[60]">{{ __('Soft-delete selected students') }}</span>
        </div>
        @if ($showDeleted)
            <div x-data="{ tooltip: false }" class="relative">
                <button
                    x-on:click="$wire.set('modalId', 'confirm-bulk-restore')"
                    class="px-4 py-2 text-neutral-600 dark:text-neutral-300 bg-neutral-100 dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-colors"
                    wire:loading.attr="disabled"
                    wire:target="$set('modalId', 'confirm-bulk-restore')"
                    x-on:mouseover="tooltip = true"
                    x-on:mouseout="tooltip = false"
                >
                    <span wire:loading wire:target="$set('modalId', 'confirm-bulk-restore')" class="animate-spin inline-block w-4 h-4 mr-2">⟳</span>
                    {{ __('Restore Selected') }}
                </button>
                <span x-show="tooltip" class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-2 py-1 text-xs text-white bg-neutral-800 rounded shadow-lg z-[60]">{{ __('Restore selected students') }}</span>
            </div>
        @endif
    </div>
</div>