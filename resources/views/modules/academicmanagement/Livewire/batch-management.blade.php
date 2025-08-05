<!-- resources/views/modules/academicmanagement/livewire/batch-management.blade.php -->
<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold">Batch Management</h2>
        <div class="flex flex-wrap gap-2">
            <button wire:click="openCreateModal" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Add Batch
            </button>
            @if (request()->routeIs('*.trashed'))
                <a href="{{ route(request()->route->getName(), ['trashed' => false]) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    Active Batches
                </a>
            @else
                <a href="{{ route(request()->route->getName() . '.trashed') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    Trashed Batches
                </a>
            @endif
        </div>
    </div>
    
    <!-- Advanced Filters -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input type="text" wire:model.debounce.300ms="search" id="search" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="Search batches..." aria-label="Search batches">
            </div>
            
            <div class="min-w-[150px]">
                <label for="filterProgramme" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Programme</label>
                <select wire:model="filterProgramme" id="filterProgramme" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Programmes</option>
                    @foreach ($programmes as $programme)
                        <option value="{{ $programme->id }}">{{ $programme->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="min-w-[150px]">
                <label for="filterStatus" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select wire:model="filterStatus" id="filterStatus" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            
            <div class="min-w-[150px]">
                <label for="filterDateFrom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date From</label>
                <input type="date" wire:model="filterDateFrom" id="filterDateFrom" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            
            <div class="min-w-[150px]">
                <label for="filterDateTo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date To</label>
                <input type="date" wire:model="filterDateTo" id="filterDateTo" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            
            <div class="flex items-end">
                <button wire:click="resetFilters" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    Reset Filters
                </button>
            </div>
        </div>
    </div>
    
    <!-- Bulk Actions -->
    @if (!empty($selectedBatches))
        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="font-medium text-blue-800 dark:text-blue-200">
                        {{ count($selectedBatches) }} batch{{ count($selectedBatches) > 1 ? 'es' : '' }} selected
                    </span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button wire:click="openBulkActionModal('delete')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                        Delete
                    </button>
                    <button wire:click="exportBatches" class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded text-sm">
                        Export
                    </button>
                    <button wire:click="$set('selectedBatches', [])" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm">
                        Clear Selection
                    </button>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Batches Table -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" role="table" aria-label="Batches table">
                <caption id="batchesTableDescription" class="sr-only">
                    A table showing batches with their details and actions
                </caption>
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr role="row">
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" wire:model="selectAll" wire:change="toggleSelectAll" 
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500"
                                aria-label="Select all batches">
                        </th>
                        <th wire:click="sortBy('name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="flex items-center">
                                Name
                                @if ($sortBy === 'name')
                                    <span class="ml-1">
                                        @if ($sortDirection === 'asc') ↑ @else ↓ @endif
                                    </span>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Programme
                        </th>
                        <th wire:click="sortBy('start_date')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="flex items-center">
                                Start Date
                                @if ($sortBy === 'start_date')
                                    <span class="ml-1">
                                        @if ($sortDirection === 'asc') ↑ @else ↓ @endif
                                    </span>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('end_date')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="flex items-center">
                                End Date
                                @if ($sortBy === 'end_date')
                                    <span class="ml-1">
                                        @if ($sortDirection === 'asc') ↑ @else ↓ @endif
                                    </span>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('status')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="flex items-center">
                                Status
                                @if ($sortBy === 'status')
                                    <span class="ml-1">
                                        @if ($sortDirection === 'asc') ↑ @else ↓ @endif
                                    </span>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('created_at')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="flex items-center">
                                Created At
                                @if ($sortBy === 'created_at')
                                    <span class="ml-1">
                                        @if ($sortDirection === 'asc') ↑ @else ↓ @endif
                                    </span>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($batches as $batch)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700" role="row">
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <input type="checkbox" wire:model="selectedBatches" value="{{ $batch->id }}" 
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500"
                                    aria-label="Select batch {{ $batch->name }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $batch->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $batch->programme->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" role="cell">
                                {{ $batch->start_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" role="cell">
                                {{ $batch->end_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if ($batch->status)
                                        bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                    @else
                                        bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                    @endif">
                                    {{ $batch->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" role="cell">
                                {{ $batch->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" role="cell">
                                <div role="group" aria-label="Batch actions">

                                    <button wire:click="edit({{ $batch->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3" aria-label="Edit batch {{ $batch->name }}">
                                        Edit
                                    </button>
                                    @if (request()->routeIs('*.trashed'))
                                        <button wire:click="restore({{ $batch->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-3" aria-label="Restore batch {{ $batch->name }}">
                                            Restore
                                        </button>
                                        <button wire:click="forceDelete({{ $batch->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" aria-label="Permanently delete batch {{ $batch->name }}">
                                            Delete Forever
                                        </button>
                                    @else
                                        <button wire:click="delete({{ $batch->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" aria-label="Delete batch {{ $batch->name }}">
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                No batches found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $batches->links('vendor.pagination.livewire-tailwind') }}
    </div>

    <!-- Create/Edit Batch Modal -->
    @if ($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click.self="closeModal">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        {{ $isEdit ? 'Edit Batch' : 'Add New Batch' }}
                    </h3>
                    
                    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'create' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Batch Name</label>
                                <input type="text" wire:model="name" id="name" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Enter batch name">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="programme_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Programme</label>
                                <select wire:model="programme_id" id="programme_id" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Programme</option>
                                    @foreach ($programmes as $programme)
                                        <option value="{{ $programme->id }}">{{ $programme->name }}</option>
                                    @endforeach
                                </select>
                                @error('programme_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                                <input type="date" wire:model="start_date" id="start_date" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                                <input type="date" wire:model="end_date" id="end_date" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('end_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                <select wire:model="status" id="status" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                <textarea wire:model="description" id="description" rows="2"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Enter batch description"></textarea>
                                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                                {{ $isEdit ? 'Update' : 'Create' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click.self="closeDeleteModal">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/3 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Confirm Delete</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Are you sure you want to delete this batch? This action can be undone.
                        </p>
                    </div>
                    <div class="flex justify-center space-x-4 mt-4">
                        <button wire:click="closeDeleteModal" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                            Cancel
                        </button>
                        <button wire:click="confirmDelete" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Bulk Action Confirmation Modal -->
    @if ($showBulkActionModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click.self="closeBulkActionModal">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/3 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Confirm Bulk Action</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Are you sure you want to {{ $bulkActionType }} the selected batches?
                            @if ($bulkActionType === 'delete')
                                This action can be undone.
                            @endif
                        </p>
                    </div>
                    <div class="flex justify-center space-x-4 mt-4">
                        <button wire:click="closeBulkActionModal" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                            Cancel
                        </button>
                        <button wire:click="confirmBulkAction" class="px-4 py-2 bg-{{ $bulkActionType === 'delete' ? 'red' : 'blue' }}-500 text-white rounded-md hover:bg-{{ $bulkActionType === 'delete' ? 'red' : 'blue' }}-600">
                            {{ ucfirst($bulkActionType) }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>