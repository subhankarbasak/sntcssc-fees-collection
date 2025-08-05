<!-- resources/views/modules/feesmanagement/livewire/fee-management.blade.php -->
<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold">Fee Management</h2>
        <div class="flex flex-wrap gap-2">
            <button wire:click="openCreateModal" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Add Fee Structure
            </button>
            @if (request()->routeIs('*.trashed'))
                <a href="{{ route(request()->route()->getName(), ['trashed' => false]) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    Active Fee Structures
                </a>
            @else
                <a href="{{ route(request()->route->getName() . '.trashed') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    Trashed Fee Structures
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
                    placeholder="Search fee structures..." aria-label="Search fee structures">
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
                <label for="filterBatch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Batch</label>
                <select wire:model="filterBatch" id="filterBatch" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Batches</option>
                    @foreach ($batches as $batch)
                        <option value="{{ $batch->id }}">{{ $batch->name }} ({{ $batch->programme->name }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="min-w-[150px]">
                <label for="filterFeeType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fee Type</label>
                <select wire:model="filterFeeType" id="filterFeeType" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Fee Types</option>
                    @foreach ($feeTypes as $feeType)
                        <option value="{{ $feeType->id }}">{{ $feeType->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="min-w-[150px]">
                <label for="filterCategory" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                <select wire:model="filterCategory" id="filterCategory" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Categories</option>
                    <option value="Unreserved">Unreserved</option>
                    <option value="OBC">OBC</option>
                    <option value="SC">SC</option>
                    <option value="ST">ST</option>
                </select>
            </div>
            
            <div class="min-w-[150px]">
                <label for="filterDateFrom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date From</label>
                <input type="date" wire:model="filterDateFrom" id="filterDateFrom" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            
            <div class="min-w-[150px]">
                <label for="filterDateTo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date To</label>
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
    @if (!empty($selectedFees))
        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="font-medium text-blue-800 dark:text-blue-200">
                        {{ count($selectedFees) }} fee structure{{ count($selectedFees) > 1 ? 's' : '' }} selected
                    </span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button wire:click="openBulkActionModal('delete')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                        Delete
                    </button>
                    <button wire:click="exportFees" class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded text-sm">
                        Export
                    </button>
                    <button wire:click="$set('selectedFees', [])" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm">
                        Clear Selection
                    </button>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Fee Structures Table -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" role="table" aria-label="Fee structures table">
                <caption id="feeStructuresTableDescription" class="sr-only">
                    A table showing fee structures with their details and actions
                </caption>
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr role="row">
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" wire:model="selectAll" wire:change="toggleSelectAll" 
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500"
                                aria-label="Select all fee structures">
                        </th>
                        <th wire:click="sortBy('fee_type_id')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="flex items-center">
                                Fee Type
                                @if ($sortBy === 'fee_type_id')
                                    <span class="ml-1">
                                        @if ($sortDirection === 'asc') ↑ @else ↓ @endif
                                    </span>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Programme
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Batch
                        </th>
                        <th wire:click="sortBy('amount')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="flex items-center">
                                Amount
                                @if ($sortBy === 'amount')
                                    <span class="ml-1">
                                        @if ($sortDirection === 'asc') ↑ @else ↓ @endif
                                    </span>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Category
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Frequency
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
                    @forelse ($feeStructures as $feeStructure)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700" role="row">
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <input type="checkbox" wire:model="selectedFees" value="{{ $feeStructure->id }}" 
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500"
                                    aria-label="Select fee structure {{ $feeStructure->feeType->name }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $feeStructure->feeType->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $feeStructure->feeType->description }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $feeStructure->batch->programme->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $feeStructure->batch->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <div class="text-sm text-gray-900 dark:text-white">₹{{ number_format($feeStructure->amount, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $feeStructure->category }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                        {{ $feeStructure->feeType->frequency === 'monthly' ? 'Monthly' : 'One-time' }}
                                    </span>
                                    @if ($feeStructure->feeType->is_refundable)
                                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full ml-1">
                                            Refundable
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" role="cell">
                                {{ $feeStructure->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" role="cell">
                                <div role="group" aria-label="Fee structure actions">
                                    <button wire:click="openEditModal({{ $feeStructure->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3" aria-label="Edit fee structure {{ $feeStructure->feeType->name }}">Edit</button>
                                    @if (request()->routeIs('*.trashed'))
                                        <button wire:click="restoreFee({{ $feeStructure->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3" aria-label="Restore fee structure {{ $feeStructure->feeType->name }}">Restore</button>
                                        <button wire:click="forceDeleteFee({{ $feeStructure->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" aria-label="Delete permanently">Delete</button>
                                    @else
                                        <button wire:click="openDeleteModal({{ $feeStructure->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" aria-label="Delete fee structure {{ $feeStructure->feeType->name }}">Delete</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr role="row">
                            <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400" role="cell">
                                No fee structures found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <button wire:click="previousPage" wire:disabled="$feeStructures->onFirstPage()" 
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Previous
                </button>
                <button wire:click="nextPage" wire:disabled="$feeStructures->onLastPage()" 
                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Next
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Showing <span class="font-medium">{{ $feeStructures->firstItem() }}</span> to <span class="font-medium">{{ $feeStructures->lastItem() }}</span> of <span class="font-medium">{{ $feeStructures->total() }}</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <button wire:click="previousPage" wire:disabled="$feeStructures->onFirstPage()" 
                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                            aria-label="Previous">
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <!-- Pagination Numbers -->
                        @foreach ($feeStructures->links()->elements[0] as $page)
                            @if ($page['url'])
                                <button wire:click="goToPage({{ $page['page'] }})" 
                                    class="{{ $feeStructures->currentPage() == $page['page'] ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-100' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600' }} relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    {{ $page['page'] }}
                                </button>
                            @else
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                                    {{ $page['page'] }}
                                </span>
                            @endif
                        @endforeach
                        
                        <button wire:click="nextPage" wire:disabled="$feeStructures->onLastPage()" 
                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                            aria-label="Next">
                            <span class="sr-only">Next</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile View -->
    <div class="md:hidden mt-4">
        @forelse ($feeStructures as $feeStructure)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 mb-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $feeStructure->feeType->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $feeStructure->batch->name }} ({{ $feeStructure->batch->programme->name }})</p>
                    </div>
                    <input type="checkbox" wire:model="selectedFees" value="{{ $feeStructure->id }}" 
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500"
                        aria-label="Select fee structure {{ $feeStructure->feeType->name }}">
                </div>
                
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Amount</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">₹{{ number_format($feeStructure->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Category</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $feeStructure->category }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Frequency</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                {{ $feeStructure->feeType->frequency === 'monthly' ? 'Monthly' : 'One-time' }}
                            </span>
                            @if ($feeStructure->feeType->is_refundable)
                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full ml-1">
                                    Refundable
                                </span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Created At</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $feeStructure->created_at->format('d M Y') }}</p>
                    </div>
                </div>
                
                <div class="mt-4 flex flex-wrap gap-2">
                    <button wire:click="openEditModal({{ $feeStructure->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm">Edit</button>
                    @if (request()->routeIs('*.trashed'))
                        <button wire:click="restoreFee({{ $feeStructure->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm">Restore</button>
                        <button wire:click="forceDeleteFee({{ $feeStructure->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm">Delete</button>
                    @else
                        <button wire:click="openDeleteModal({{ $feeStructure->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm">Delete</button>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 text-center">
                <p class="text-gray-500 dark:text-gray-400">No fee structures found.</p>
            </div>
        @endforelse
    </div>
    
    <!-- Create Fee Structure Modal -->
    <x-modal wire:model="showCreateModal" title="Add Fee Structure">
        <form wire:submit.prevent="createFee">
            <div class="space-y-4">
                <div>
                    <x-input-label for="title" :value="__('Fee Type Name')" />
                    <x-text-input wire:model="title" id="title" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="description" :value="__('Description')" />
                    <textarea wire:model="description" id="description" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" rows="3"></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="frequency" :value="__('Frequency')" />
                    <select wire:model="frequency" id="frequency" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="one_time">One-time</option>
                        <option value="monthly">Monthly</option>
                    </select>
                    <x-input-error :messages="$errors->get('frequency')" class="mt-2" />
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" wire:model="is_refundable" id="is_refundable" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <x-input-label for="is_refundable" :value="__('Refundable')" class="ml-2" />
                </div>
                
                <div>
                    <x-input-label for="selectedProgramme" :value="__('Programme')" />
                    <select wire:model="selectedProgramme" wire:change="$refresh" id="selectedProgramme" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="">Select a programme</option>
                        @foreach ($programmes as $programme)
                            <option value="{{ $programme->id }}">{{ $programme->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('selectedProgramme')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="selectedBatch" :value="__('Batch')" />
                    <select wire:model="selectedBatch" id="selectedBatch" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="">Select a batch</option>
                        @foreach ($this->getBatchesForProgramme($selectedProgramme) as $batch)
                            <option value="{{ $batch->id }}">{{ $batch->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('selectedBatch')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="amount" :value="__('Amount')" />
                    <x-text-input wire:model="amount" id="amount" class="block mt-1 w-full" type="number" step="0.01" min="0" required />
                    <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="category" :value="__('Category')" />
                    <select wire:model="category" id="category" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="Unreserved">Unreserved</option>
                        <option value="OBC">OBC</option>
                        <option value="SC">SC</option>
                        <option value="ST">ST</option>
                    </select>
                    <x-input-error :messages="$errors->get('category')" class="mt-2" />
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$toggle('showCreateModal')" :disabled="false">
                    {{ __('Cancel') }}
                </x-secondary-button>
                
                <x-primary-button class="ml-3" :disabled="{{ $errors->count() > 0 }}">
                    {{ __('Create') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
    
    <!-- Edit Fee Structure Modal -->
    <x-modal wire:model="showEditModal" title="Edit Fee Structure">
        <form wire:submit.prevent="updateFee">
            <div class="space-y-4">
                <div>
                    <x-input-label for="title" :value="__('Fee Type Name')" />
                    <x-text-input wire:model="title" id="title" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="description" :value="__('Description')" />
                    <textarea wire:model="description" id="description" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" rows="3"></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="frequency" :value="__('Frequency')" />
                    <select wire:model="frequency" id="frequency" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="one_time">One-time</option>
                        <option value="monthly">Monthly</option>
                    </select>
                    <x-input-error :messages="$errors->get('frequency')" class="mt-2" />
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" wire:model="is_refundable" id="is_refundable" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <x-input-label for="is_refundable" :value="__('Refundable')" class="ml-2" />
                </div>
                
                <div>
                    <x-input-label for="selectedProgramme" :value="__('Programme')" />
                    <select wire:model="selectedProgramme" wire:change="$refresh" id="selectedProgramme" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="">Select a programme</option>
                        @foreach ($programmes as $programme)
                            <option value="{{ $programme->id }}">{{ $programme->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('selectedProgramme')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="selectedBatch" :value="__('Batch')" />
                    <select wire:model="selectedBatch" id="selectedBatch" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="">Select a batch</option>
                        @foreach ($this->getBatchesForProgramme($selectedProgramme) as $batch)
                            <option value="{{ $batch->id }}">{{ $batch->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('selectedBatch')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="amount" :value="__('Amount')" />
                    <x-text-input wire:model="amount" id="amount" class="block mt-1 w-full" type="number" step="0.01" min="0" required />
                    <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="category" :value="__('Category')" />
                    <select wire:model="category" id="category" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="Unreserved">Unreserved</option>
                        <option value="OBC">OBC</option>
                        <option value="SC">SC</option>
                        <option value="ST">ST</option>
                    </select>
                    <x-input-error :messages="$errors->get('category')" class="mt-2" />
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$toggle('showEditModal')" :disabled="false">
                    {{ __('Cancel') }}
                </x-secondary-button>
                
                <x-primary-button class="ml-3" :disabled="{{ $errors->count() > 0 }}">
                    {{ __('Update') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
    
    <!-- Delete Fee Structure Modal -->
    <x-modal wire:model="showDeleteModal" title="Delete Fee Structure">
        <div class="space-y-4">
            <p>Are you sure you want to delete the fee structure "{{ $selectedFee->feeType->name }}"?</p>
            <p class="text-red-600 dark:text-red-400">This action will move the fee structure to trash. You can restore it later if needed.</p>
        </div>
        
        <div class="mt-6 flex justify-end">
            <x-secondary-button wire:click="$toggle('showDeleteModal')" :disabled="false">
                {{ __('Cancel') }}
            </x-secondary-button>
            
            <x-danger-button class="ml-3" wire:click="deleteFee">
                {{ __('Delete') }}
            </x-danger-button>
        </div>
    </x-modal>
    
    <!-- Bulk Action Modal -->
    <x-modal wire:model="showBulkActionModal" title="Confirm Bulk Action">
        <div class="space-y-4">
            <p>Are you sure you want to {{ $bulkAction }} {{ count($selectedFees) }} fee structure{{ count($selectedFees) > 1 ? 's' : '' }}?</p>
            @if ($bulkAction === 'delete')
                <p class="text-red-600 dark:text-red-400">This action will move the selected fee structures to trash. You can restore them later if needed.</p>
            @endif
        </div>
        
        <div class="mt-6 flex justify-end">
            <x-secondary-button wire:click="$toggle('showBulkActionModal')" :disabled="false">
                {{ __('Cancel') }}
            </x-secondary-button>
            
            <x-danger-button class="ml-3" wire:click="performBulkAction">
                {{ __('Confirm') }}
            </x-danger-button>
        </div>
    </x-modal>
    
    <!-- Restore Modal -->
    <x-modal wire:model="showRestoreModal" title="Restore Fee Structure">
        <div class="space-y-4">
            <p>Are you sure you want to restore the selected fee structure?</p>
        </div>
        
        <div class="mt-6 flex justify-end">
            <x-secondary-button wire:click="$toggle('showRestoreModal')" :disabled="false">
                {{ __('Cancel') }}
            </x-secondary-button>
            
            <x-primary-button class="ml-3" wire:click="restoreFee({{ $selectedFee->id }})">
                {{ __('Restore') }}
            </x-primary-button>
        </div>
    </x-modal>
</div>