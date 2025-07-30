<div x-show="$wire.showModal" x-on:open-modal.window="if ($event.detail.id === 'student-form') $wire.showModal = true" x-on:close-modal.window="if ($event.detail.id === 'student-form') $wire.showModal = false" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="fixed inset-0 modal-backdrop bg-neutral-900/50 flex items-center justify-center z-50" x-cloak>
    <div class="modal-content bg-white dark:bg-neutral-800 rounded-2xl shadow-xl max-w-4xl w-full p-8 overflow-y-auto max-h-[90vh]">
        <form wire:submit.prevent="save" class="space-y-8" wire:dirty.class="border-yellow-500">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">{{ $editingId ? __('Edit Student') : __('Add New Student') }}</h2>
                <button type="button" wire:click="closeModal" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 transition-colors" aria-label="{{ __('Close') }}">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            @if ($errors->has('form.*'))
                <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 p-4 rounded-lg">
                    <p class="text-red-700 dark:text-red-300 font-semibold">{{ __('Please fix the following errors:') }}</p>
                    <ul class="list-disc list-inside text-red-600 dark:text-red-400 mt-2">
                        @foreach ($errors->get('form.*') as $fieldErrors)
                            @foreach ($fieldErrors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 p-4 rounded-lg mt-4">
                    <p class="text-red-700 dark:text-red-300 font-semibold">{{ __('Error:') }}</p>
                    <p class="text-red-600 dark:text-red-400">{{ session('error') }}</p>
                </div>
        <script>
            document.addEventListener('livewire:initialized', () => {
                flasher.error('{{ session('error') }}');
            });
        </script>
            @endif
            <p class="text-neutral-600 dark:text-neutral-400">{{ $editingId ? __('Update the student\'s details below') : __('Enter the student\'s details below') }}</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Application Number') }}</label>
                    <input
                        wire:model.debounce.500ms="form.application_number"
                        type="text"
                        placeholder="APP2025001"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        required
                        wire:loading.attr="disabled"
                    />
                    @error('form.application_number') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Admission Test Roll No') }}</label>
                    <input
                        wire:model.debounce.500ms="form.admission_test_roll_no"
                        type="text"
                        placeholder="ATR2025001"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.admission_test_roll_no') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Programme Name') }}</label>
                    <input
                        wire:model.debounce.500ms="form.programme_name"
                        type="text"
                        placeholder="B.Tech Computer Science"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        required
                        wire:loading.attr="disabled"
                    />
                    @error('form.programme_name') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Batch') }}</label>
                    <input
                        wire:model.debounce.500ms="form.batch"
                        type="text"
                        placeholder="2025"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        required
                        wire:loading.attr="disabled"
                    />
                    @error('form.batch') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Student ID') }}</label>
                    <input
                        wire:model.debounce.500ms="form.student_id"
                        type="text"
                        placeholder="STU2025001"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        required
                        wire:loading.attr="disabled"
                    />
                    @error('form.student_id') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Section') }}</label>
                    <input
                        wire:model.debounce.500ms="form.section"
                        type="text"
                        placeholder="A"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.section') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('First Name') }}</label>
                    <input
                        wire:model.debounce.500ms="form.first_name"
                        type="text"
                        placeholder="Amit"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        required
                        wire:loading.attr="disabled"
                    />
                    @error('form.first_name') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Last Name') }}</label>
                    <input
                        wire:model.debounce.500ms="form.last_name"
                        type="text"
                        placeholder="Sharma"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        required
                        wire:loading.attr="disabled"
                    />
                    @error('form.last_name') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('District') }}</label>
                    <input
                        wire:model.debounce.500ms="form.district"
                        type="text"
                        placeholder="Mumbai"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.district') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Address') }}</label>
                    <textarea
                        wire:model.lazy="form.address"
                        placeholder="123, Andheri East, Mumbai"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    ></textarea>
                    @error('form.address') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Category') }}</label>
                    <select
                        wire:model.debounce.500ms="form.category"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        required
                        wire:loading.attr="disabled"
                    >
                        <option value="Unreserved">{{ __('Unreserved') }}</option>
                        <option value="SC">{{ __('SC') }}</option>
                        <option value="ST">{{ __('ST') }}</option>
                        <option value="OBC">{{ __('OBC') }}</option>
                    </select>
                    @error('form.category') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Date of Birth') }}</label>
                    <input
                        wire:model.debounce.500ms="form.dob"
                        type="date"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.dob') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Gender') }}</label>
                    <select
                        wire:model.debounce.500ms="form.gender"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    >
                        <option value="">{{ __('Select Gender') }}</option>
                        <option value="Male">{{ __('Male') }}</option>
                        <option value="Female">{{ __('Female') }}</option>
                        <option value="Other">{{ __('Other') }}</option>
                    </select>
                    @error('form.gender') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Email') }}</label>
                    <input
                        wire:model.debounce.500ms="form.email"
                        type="email"
                        placeholder="student@example.com"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        required
                        wire:loading.attr="disabled"
                    />
                    @error('form.email') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Alternate Email') }}</label>
                    <input
                        wire:model.debounce.500ms="form.alternate_email"
                        type="email"
                        placeholder="alternate@example.com"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.alternate_email') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Mobile') }}</label>
                    <input
                        wire:model.debounce.500ms="form.mobile"
                        type="text"
                        placeholder="9876543210"
                        x-mask="9999999999"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.mobile') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Alternate Mobile') }}</label>
                    <input
                        wire:model.debounce.500ms="form.alternate_mobile"
                        type="text"
                        placeholder="8765432109"
                        x-mask="9999999999"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.alternate_mobile') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('WhatsApp') }}</label>
                    <input
                        wire:model.debounce.500ms="form.whatsapp"
                        type="text"
                        placeholder="9876543210"
                        x-mask="9999999999"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.whatsapp') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="flex items-center gap-2">
                        <input wire:model="form.is_pwbd" type="checkbox" class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-neutral-300 dark:border-neutral-600 rounded" wire:loading.attr="disabled" />
                        <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Person with Benchmark Disability (PwBD)') }}</span>
                    </label>
                    @error('form.is_pwbd') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Occupation') }}</label>
                    <input
                        wire:model.debounce.500ms="form.occupation"
                        type="text"
                        placeholder="Student"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.occupation') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Father\'s Name') }}</label>
                    <input
                        wire:model.debounce.500ms="form.father_name"
                        type="text"
                        placeholder="Rakesh Sharma"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.father_name') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Mother\'s Name') }}</label>
                    <input
                        wire:model.debounce.500ms="form.mother_name"
                        type="text"
                        placeholder="Sunita Sharma"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.mother_name') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Father\'s Occupation') }}</label>
                    <input
                        wire:model.debounce.500ms="form.father_occupation"
                        type="text"
                        placeholder="Engineer"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.father_occupation') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Mother\'s Occupation') }}</label>
                    <input
                        wire:model.debounce.500ms="form.mother_occupation"
                        type="text"
                        placeholder="Teacher"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.mother_occupation') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Family Income') }}</label>
                    <input
                        wire:model.debounce.500ms="form.family_income"
                        type="number"
                        step="0.01"
                        placeholder="1200000.00"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.family_income') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Selection Type') }}</label>
                    <input
                        wire:model.debounce.500ms="form.selection_type"
                        type="text"
                        placeholder="Merit"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.selection_type') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Score A') }}</label>
                    <input
                        wire:model.debounce.500ms="form.score_A"
                        type="number"
                        step="0.01"
                        placeholder="85.50"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.score_A') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Score B') }}</label>
                    <input
                        wire:model.debounce.500ms="form.score_B"
                        type="number"
                        step="0.01"
                        placeholder="78.25"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.score_B') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Score C') }}</label>
                    <input
                        wire:model.debounce.500ms="form.score_C"
                        type="number"
                        step="0.01"
                        placeholder="92.00"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.score_C') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Score D') }}</label>
                    <input
                        wire:model.debounce.500ms="form.score_D"
                        type="number"
                        step="0.01"
                        placeholder="88.75"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.score_D') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Status') }}</label>
                    <input
                        wire:model.debounce.500ms="form.status"
                        type="text"
                        placeholder="Active"
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    />
                    @error('form.status') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Note') }}</label>
                    <textarea
                        wire:model.lazy="form.note"
                        placeholder="Additional notes..."
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    ></textarea>
                    @error('form.note') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Remarks') }}</label>
                    <textarea
                        wire:model.lazy="form.remarks"
                        placeholder="Remarks..."
                        class="w-full p-3 border rounded-lg text-neutral-900 dark:text-neutral-100 bg-white dark:bg-neutral-800 border-neutral-300 dark:border-neutral-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        wire:loading.attr="disabled"
                    ></textarea>
                    @error('form.remarks') <span class="text-red-600 font-semibold text-base mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" wire:click="closeModal" class="px-4 py-2 text-neutral-600 dark:text-neutral-300 bg-neutral-100 dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-colors" wire:loading.attr="disabled" wire:target="closeModal">
                    <span wire:loading wire:target="closeModal" class="animate-spin inline-block w-4 h-4 mr-2">⟳</span>
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading wire:target="save" class="animate-spin inline-block w-4 h-4 mr-2">⟳</span>
                    {{ $loading ? __('Saving...') : __('Save') }}
                </button>
            </div>
        </form>
    </div>
</div>