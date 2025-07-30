<div class="rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6 shadow-sm">
    <x-auth-header :title="__('Student Management')" :description="__('Manage student records with ease, including creation, editing, deletion, and export functionalities.')" />
    <div aria-live="polite" class="mt-4">
        <x-auth-session-status class="text-center text-lg font-medium text-green-600 dark:text-green-400" :status="session('status')" />
        @if (session('error'))
            <div class="text-center text-lg font-medium text-red-600 dark:text-red-400">
                {{ session('error') }}
            </div>
        @endif
    </div>
</div>