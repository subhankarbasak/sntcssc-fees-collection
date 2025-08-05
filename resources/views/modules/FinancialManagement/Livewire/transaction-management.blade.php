<!-- resources/views/modules/financialmanagement/livewire/transaction-management.blade.php -->
<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold">Transaction Management</h2>
        <div class="flex flex-wrap gap-2">
            <button wire:click="openCreateModal" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Add Transaction
            </button>
        </div>
    </div>
    
    <!-- Advanced Filters -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input type="text" wire:model.debounce.300ms="search" id="search" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="Search transactions..." aria-label="Search transactions">
            </div>
            
            <div class="min-w-[150px]">
                <label for="filterType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                <select wire:model="filterType" id="filterType" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Types</option>
                    <option value="payment">Payment</option>
                    <option value="deposit">Deposit</option>
                    <option value="refund">Refund</option>
                </select>
            </div>
            
            <div class="min-w-[150px]">
                <label for="filterStatus" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select wire:model="filterStatus" id="filterStatus" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
            
            <div class="min-w-[150px]">
                <label for="filterPaymentMethod" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Method</label>
                <select wire:model="filterPaymentMethod" id="filterPaymentMethod" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Methods</option>
                    <option value="cash">Cash</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="debit_card">Debit Card</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="upi">UPI</option>
                </select>
            </div>
            
            <div class="min-w-[150px]">
                <label for="filterStudent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student</label>
                <select wire:model="filterStudent" id="filterStudent" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Students</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}">{{ $student->user->name }} ({{ $student->student_id }})</option>
                    @endforeach
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
    @if (!empty($selectedTransactions))
        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="font-medium text-blue-800 dark:text-blue-200">
                        {{ count($selectedTransactions) }} transaction{{ count($selectedTransactions) > 1 ? 's' : '' }} selected
                    </span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button wire:click="exportTransactions" class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded text-sm">
                        Export
                    </button>
                    <button wire:click="$set('selectedTransactions', [])" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm">
                        Clear Selection
                    </button>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Transactions Table -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" role="table" aria-label="Transactions table">
                <caption id="transactionsTableDescription" class="sr-only">
                    A table showing transactions with their details and actions
                </caption>
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr role="row">
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" wire:model="selectAll" wire:change="toggleSelectAll" 
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500"
                                aria-label="Select all transactions">
                        </th>
                        <th wire:click="sortBy('transaction_date')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="flex items-center">
                                Date
                                @if ($sortBy === 'transaction_date')
                                    <span class="ml-1">
                                        @if ($sortDirection === 'asc') ↑ @else ↓ @endif
                                    </span>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Student
                        </th>
                        <th wire:click="sortBy('type')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="flex items-center">
                                Type
                                @if ($sortBy === 'type')
                                    <span class="ml-1">
                                        @if ($sortDirection === 'asc') ↑ @else ↓ @endif
                                    </span>
                                @endif
                            </div>
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
                            Payment Method
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($transactions as $transaction)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700" role="row">
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <input type="checkbox" wire:model="selectedTransactions" value="{{ $transaction->id }}" 
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500"
                                    aria-label="Select transaction {{ $transaction->id }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" role="cell">
                                {{ $transaction->transaction_date->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $transaction->student->user->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $transaction->student->student_id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if ($transaction->type === 'payment')
                                        bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                    @elseif ($transaction->type === 'deposit')
                                        bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100
                                    @else
                                        bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                    @endif">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" role="cell">
                                ₹{{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" role="cell">
                                {{ ucfirst($transaction->payment_method) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if ($transaction->status === 'completed')
                                        bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                    @elseif ($transaction->status === 'pending')
                                        bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                    @else
                                        bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                    @endif">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" role="cell">
                                <div role="group" aria-label="Transaction actions">
                                    <button wire:click="openViewModal({{ $transaction->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" aria-label="View transaction {{ $transaction->id }}">View</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr role="row">
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400" role="cell">
                                No transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <button wire:click="previousPage" wire:disabled="$transactions->onFirstPage()" 
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Previous
                </button>
                <button wire:click="nextPage" wire:disabled="$transactions->onLastPage()" 
                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Next
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Showing <span class="font-medium">{{ $transactions->firstItem() }}</span> to <span class="font-medium">{{ $transactions->lastItem() }}</span> of <span class="font-medium">{{ $transactions->total() }}</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <button wire:click="previousPage" wire:disabled="$transactions->onFirstPage()" 
                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                            aria-label="Previous">
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <!-- Pagination Numbers -->
                        @foreach ($transactions->links()->elements[0] as $page)
                            @if ($page['url'])
                                <button wire:click="goToPage({{ $page['page'] }})" 
                                    class="{{ $transactions->currentPage() == $page['page'] ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-100' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600' }} relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    {{ $page['page'] }}
                                </button>
                            @else
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                                    {{ $page['page'] }}
                                </span>
                            @endif
                        @endforeach
                        
                        <button wire:click="nextPage" wire:disabled="$transactions->onLastPage()" 
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
        @forelse ($transactions as $transaction)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 mb-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $transaction->student->user->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $transaction->student->student_id }}</p>
                    </div>
                    <input type="checkbox" wire:model="selectedTransactions" value="{{ $transaction->id }}" 
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500"
                        aria-label="Select transaction {{ $transaction->id }}">
                </div>
                
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Type</p>
                        <p class="text-sm font-medium">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if ($transaction->type === 'payment')
                                    bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                @elseif ($transaction->type === 'deposit')
                                    bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100
                                @else
                                    bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                @endif">
                                {{ ucfirst($transaction->type) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Amount</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">₹{{ number_format($transaction->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Payment Method</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($transaction->payment_method) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                        <p class="text-sm font-medium">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if ($transaction->status === 'completed')
                                    bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                @elseif ($transaction->status === 'pending')
                                    bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                @else
                                    bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                @endif">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Date</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $transaction->transaction_date->format('d M Y H:i') }}</p>
                    </div>
                </div>
                
                <div class="mt-4 flex flex-wrap gap-2">
                    <button wire:click="openViewModal({{ $transaction->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm">View</button>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 text-center">
                <p class="text-gray-500 dark:text-gray-400">No transactions found.</p>
            </div>
        @endforelse
    </div>
    
    <!-- Create Transaction Modal -->
    <x-modal wire:model="showCreateModal" title="Add Transaction">
        <form wire:submit.prevent="createTransaction">
            <div class="space-y-4">
                <div>
                    <x-input-label for="student_id" :value="__('Student')" />
                    <select wire:model="student_id" id="student_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="">Select a student</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}">{{ $student->user->name }} ({{ $student->student_id }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="type" :value="__('Type')" />
                    <select wire:model="type" id="type" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="payment">Payment</option>
                        <option value="deposit">Deposit</option>
                        <option value="refund">Refund</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="amount" :value="__('Amount')" />
                    <x-text-input wire:model="amount" id="amount" class="block mt-1 w-full" type="number" step="0.01" min="0" required />
                    <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="payment_method" :value="__('Payment Method')" />
                    <select wire:model="payment_method" id="payment_method" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="cash">Cash</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="debit_card">Debit Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="upi">UPI</option>
                    </select>
                    <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="reference_id" :value="__('Reference ID')" />
                    <x-text-input wire:model="reference_id" id="reference_id" class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('reference_id')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="notes" :value="__('Notes')" />
                    <textarea wire:model="notes" id="notes" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" rows="3"></textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
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
    
    <!-- View Transaction Modal -->
    <x-modal wire:model="showViewModal" title="Transaction Details">
        @if ($selectedTransaction)
            <div class="space-y-4">
                <div>
                    <x-input-label :value="__('Student')" />
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $selectedTransaction->student->user->name }} ({{ $selectedTransaction->student->student_id }})</p>
                </div>
                
                <div>
                    <x-input-label :value="__('Type')" />
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if ($selectedTransaction->type === 'payment')
                                bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                            @elseif ($selectedTransaction->type === 'deposit')
                                bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100
                            @else
                                bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                            @endif">
                            {{ ucfirst($selectedTransaction->type) }}
                        </span>
                    </p>
                </div>
                
                <div>
                    <x-input-label :value="__('Amount')" />
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">₹{{ number_format($selectedTransaction->amount, 2) }}</p>
                </div>
                
                <div>
                    <x-input-label :value="__('Payment Method')" />
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst($selectedTransaction->payment_method) }}</p>
                </div>
                
                <div>
                    <x-input-label :value="__('Status')" />
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if ($selectedTransaction->status === 'completed')
                                bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                            @elseif ($selectedTransaction->status === 'pending')
                                bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                            @else
                                bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                            @endif">
                            {{ ucfirst($selectedTransaction->status) }}
                        </span>
                    </p>
                </div>
                
                <div>
                    <x-input-label :value="__('Reference ID')" />
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $selectedTransaction->reference_id ?: 'N/A' }}</p>
                </div>
                
                <div>
                    <x-input-label :value="__('Transaction Date')" />
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $selectedTransaction->transaction_date->format('d M Y H:i:s') }}</p>
                </div>
                
                <div>
                    <x-input-label :value="__('Notes')" />
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $selectedTransaction->notes ?: 'N/A' }}</p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$toggle('showViewModal')" :disabled="false">
                    {{ __('Close') }}
                </x-secondary-button>
            </div>
        @endif
    </x-modal>
</div>