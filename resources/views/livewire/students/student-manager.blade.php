<div class="flex h-full w-full flex-1 flex-col gap-6">
    <!-- Header -->
    @include('livewire.students.header')

    <!-- Filters -->
    @include('livewire.students.filters')

    <!-- Actions -->
    @include('livewire.students.actions')

    <!-- Table -->
    @include('livewire.students.table')

    <!-- Modals -->
    @include('livewire.students.modals.add-edit-modal')
    @include('livewire.students.modals.bulk-delete-modal')
    @include('livewire.students.modals.bulk-restore-modal')
</div>