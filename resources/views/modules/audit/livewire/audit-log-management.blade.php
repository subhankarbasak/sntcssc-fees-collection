<!-- resources/views/modules/audit/livewire/audit-log-management.blade.php -->
<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold">Audit Logs</h2>
        <div class="flex flex-wrap gap-2">
            <button wire:click="exportAuditLogs" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded">
                Export Logs
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
                    placeholder="Search logs..." aria-label="Search audit logs">
            </div>
            
            <div class="min-w-[150px]">
                <label for="filterAction" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Action</label>
                <select wire:model="filterAction" id="filterAction" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Actions</option>
                    <option value="create">Create</option>
                    <option value="update">Update</option>
                    <option value="delete">Delete</option>
                    <option value="restore">Restore</option>
                    <option value="force_delete">Force Delete</option>
                    <option value="login">Login</option>
                    <option value="logout">Logout</option>
                    <option value="export">Export</option>
                    <option value="import">Import</option>
                </select>
            </div>
            
            <div class="min-w-[150px]">
                <label for="filterModel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Model</label>
                <select wire:model="filterModel" id="filterModel" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Models</option>
                    <option value="User">User</option>
                    <option value="Student">Student</option>
                    <option value="Document">Document</option>
                    <option value="Transaction">Transaction</option>
                    <option value="Enrollment">Enrollment</option>
                    <option value="Programme">Programme</option>
                    <option value="Batch">Batch</option>
                    <option value="FeeStructure">Fee Structure</option>
                    <option value="StudentFee">Student Fee</option>
                </select>
            </div>
            
            <div class="min-w-[150px]">
                <label for="filterUser" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">User</label>
                <select wire:model="filterUser" id="filterUser" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Users</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->name }}">{{ $user->name }}</option>
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
    
    <!-- Audit Logs Table -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" role="table" aria-label="Audit logs table">
                <caption id="auditLogsTableDescription" class="sr-only">
                    A table showing audit logs with their details
                </caption>
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr role="row">
                        <th wire:click="sortBy('created_at')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="flex items-center">
                                Date & Time
                                @if ($sortBy === 'created_at')
                                    <span class="ml-1">
                                        @if ($sortDirection === 'asc') ↑ @else ↓ @endif
                                    </span>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('action')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="flex items-center">
                                Action
                                @if ($sortBy === 'action')
                                    <span class="ml-1">
                                        @if ($sortDirection === 'asc') ↑ @else ↓ @endif
                                    </span>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            User
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Model
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Description
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            IP Address
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($auditLogs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700" role="row">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" role="cell">
                                {{ $log->created_at->format('d M Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if ($log->action === 'create')
                                        bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                    @elseif ($log->action === 'update')
                                        bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100
                                    @elseif ($log->action === 'delete')
                                        bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                    @elseif ($log->action === 'restore')
                                        bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                    @elseif ($log->action === 'force_delete')
                                        bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100
                                    @elseif ($log->action === 'login' || $log->action === 'logout')
                                        bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-100
                                    @elseif ($log->action === 'export' || $log->action === 'import')
                                        bg-pink-100 text-pink-800 dark:bg-pink-800 dark:text-pink-100
                                    @else
                                        bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                    @endif">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" role="cell">
                                {{ $log->user ? $log->user->name : 'System' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" role="cell">
                                <div>
                                    <div>{{ class_basename($log->model_type) }}</div>
                                    @if ($log->model_id)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $log->model_id }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white" role="cell">
                                {{ $log->description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" role="cell">
                                {{ $log->ip_address }}
                            </td>
                        </tr>
                    @empty
                        <tr role="row">
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400" role="cell">
                                No audit logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <button wire:click="previousPage" wire:disabled="$auditLogs->onFirstPage()" 
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Previous
                </button>
                <button wire:click="nextPage" wire:disabled="$auditLogs->onLastPage()" 
                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Next
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Showing <span class="font-medium">{{ $auditLogs->firstItem() }}</span> to <span class="font-medium">{{ $auditLogs->lastItem() }}</span> of <span class="font-medium">{{ $auditLogs->total() }}</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <button wire:click="previousPage" wire:disabled="$auditLogs->onFirstPage()" 
                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                            aria-label="Previous">
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <!-- Pagination Numbers -->
                        @foreach ($auditLogs->links()->elements[0] as $page)
                            @if ($page['url'])
                                <button wire:click="goToPage({{ $page['page'] }})" 
                                    class="{{ $auditLogs->currentPage() == $page['page'] ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-100' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600' }} relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    {{ $page['page'] }}
                                </button>
                            @else
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                                    {{ $page['page'] }}
                                </span>
                            @endif
                        @endforeach
                        
                        <button wire:click="nextPage" wire:disabled="$auditLogs->onLastPage()" 
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
        @forelse ($auditLogs as $log)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 mb-4">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if ($log->action === 'create')
                                bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                            @elseif ($log->action === 'update')
                                bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100
                            @elseif ($log->action === 'delete')
                                bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                            @elseif ($log->action === 'restore')
                                bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                            @elseif ($log->action === 'force_delete')
                                bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100
                            @elseif ($log->action === 'login' || $log->action === 'logout')
                                bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-100
                            @elseif ($log->action === 'export' || $log->action === 'import')
                                bg-pink-100 text-pink-800 dark:bg-pink-800 dark:text-pink-100
                            @else
                                bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                            @endif">
                            {{ ucfirst($log->action) }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $log->created_at->format('d M Y H:i') }}
                    </div>
                </div>
                
                <div class="mt-2">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $log->description }}</p>
                    <div class="mt-1 grid grid-cols-2 gap-2">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">User</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $log->user ? $log->user->name : 'System' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Model</p>
                            <p class="text-sm text-gray-900 dark:text-white">
                                {{ class_basename($log->model_type) }}
                                @if ($log->model_id)
                                    <span class="text-xs text-gray-500 dark:text-gray-400">(ID: {{ $log->model_id }})</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">IP Address</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $log->ip_address }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 text-center">
                <p class="text-gray-500 dark:text-gray-400">No audit logs found.</p>
            </div>
        @endforelse
    </div>
</div>