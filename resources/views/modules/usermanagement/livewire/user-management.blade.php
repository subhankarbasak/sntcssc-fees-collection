<!-- resources/views/modules/usermanagement/livewire/user-management.blade.php -->
<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold">User Management</h2>
        <div class="flex flex-wrap gap-2">
            <button wire:click="openCreateModal" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Add User
            </button>
            @if (request()->routeIs('*.trashed'))
                <a href="{{ route(request()->route()->getName(), ['trashed' => false]) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    Active Users
                </a>
            @else
                <a href="{{ route(request()->route->getName() . '.trashed') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    Trashed Users
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
                    placeholder="Search users..." aria-label="Search users">
            </div>
            
            <div class="min-w-[150px]">
                <label for="filterRole" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                <select wire:model="filterRole" id="filterRole" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">All Roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
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
    @if (!empty($selectedUsers))
        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="font-medium text-blue-800 dark:text-blue-200">
                        {{ count($selectedUsers) }} user{{ count($selectedUsers) > 1 ? 's' : '' }} selected
                    </span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button wire:click="openBulkActionModal('delete')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                        Delete
                    </button>
                    <button wire:click="openBulkActionModal('activate')" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                        Activate
                    </button>
                    <button wire:click="openBulkActionModal('deactivate')" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                        Deactivate
                    </button>
                    <button wire:click="exportUsers" class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded text-sm">
                        Export
                    </button>
                    <button wire:click="$set('selectedUsers', [])" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm">
                        Clear Selection
                    </button>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Users Table -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" role="table" aria-label="Users table">
                <caption id="usersTableDescription" class="sr-only">
                    A table showing users with their details and actions
                </caption>
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr role="row">
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" wire:model="selectAll" wire:change="toggleSelectAll" 
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500"
                                aria-label="Select all users">
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
                        <th wire:click="sortBy('email')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="flex items-center">
                                Email
                                @if ($sortBy === 'email')
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Roles
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
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700" role="row">
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <input type="checkbox" wire:model="selectedUsers" value="{{ $user->id }}" 
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500"
                                    aria-label="Select user {{ $user->name }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if ($user->status)
                                        bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                    @else
                                        bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                    @endif">
                                    {{ $user->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $user->roles->pluck('name')->implode(', ') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" role="cell">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" role="cell">
                                <div role="group" aria-label="User actions">
                                    <button wire:click="openEditModal({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3" aria-label="Edit user {{ $user->name }}">Edit</button>
                                    @if (request()->routeIs('*.trashed'))
                                        <button wire:click="restoreUser({{ $user->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3" aria-label="Restore user {{ $user->name }}">Restore</button>
                                        <button wire:click="forceDeleteUser({{ $user->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" aria-label="Delete permanently">Delete</button>
                                    @else
                                        <button wire:click="openDeleteModal({{ $user->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" aria-label="Delete user {{ $user->name }}">Delete</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr role="row">
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400" role="cell">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <button wire:click="previousPage" wire:disabled="$users->onFirstPage()" 
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Previous
                </button>
                <button wire:click="nextPage" wire:disabled="$users->onLastPage()" 
                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Next
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Showing <span class="font-medium">{{ $users->firstItem() }}</span> to <span class="font-medium">{{ $users->lastItem() }}</span> of <span class="font-medium">{{ $users->total() }}</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <button wire:click="previousPage" wire:disabled="$users->onFirstPage()" 
                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                            aria-label="Previous">
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <!-- Pagination Numbers -->
                        @foreach ($users->links()->elements[0] as $page)
                            @if ($page['url'])
                                <button wire:click="goToPage({{ $page['page'] }})" 
                                    class="{{ $users->currentPage() == $page['page'] ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-100' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600' }} relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    {{ $page['page'] }}
                                </button>
                            @else
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                                    {{ $page['page'] }}
                                </span>
                            @endif
                        @endforeach
                        
                        <button wire:click="nextPage" wire:disabled="$users->onLastPage()" 
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
        @forelse ($users as $user)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 mb-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                    </div>
                    <input type="checkbox" wire:model="selectedUsers" value="{{ $user->id }}" 
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500"
                        aria-label="Select user {{ $user->name }}">
                </div>
                
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                        <p class="text-sm font-medium">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if ($user->status)
                                    bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                @else
                                    bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                @endif">
                                {{ $user->status ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Roles</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->roles->pluck('name')->implode(', ') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Created At</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->created_at->format('d M Y') }}</p>
                    </div>
                </div>
                
                <div class="mt-4 flex flex-wrap gap-2">
                    <button wire:click="openEditModal({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm">Edit</button>
                    @if (request()->routeIs('*.trashed'))
                        <button wire:click="restoreUser({{ $user->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm">Restore</button>
                        <button wire:click="forceDeleteUser({{ $user->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm">Delete</button>
                    @else
                        <button wire:click="openDeleteModal({{ $user->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm">Delete</button>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 text-center">
                <p class="text-gray-500 dark:text-gray-400">No users found.</p>
            </div>
        @endforelse
    </div>
    
    <!-- Create User Modal -->
    <x-modal wire:model="showCreateModal" title="Add User">
        <form wire:submit.prevent="createUser">
            <div class="space-y-4">
                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="roles" :value="__('Roles')" />
                    <div class="mt-1 space-y-2">
                        @foreach ($roles as $role)
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="roles" value="{{ $role->name }}" id="role_{{ $role->id }}" 
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <label for="role_{{ $role->id }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $role->name }}</label>
                            </div>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" wire:model="status" id="status" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <x-input-label for="status" :value="__('Active')" class="ml-2" />
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
    
    <!-- Edit User Modal -->
    <x-modal wire:model="showEditModal" title="Edit User">
        <form wire:submit.prevent="updateUser">
            <div class="space-y-4">
                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="password" :value="__('Password (leave blank to keep current)')" />
                    <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="roles" :value="__('Roles')" />
                    <div class="mt-1 space-y-2">
                        @foreach ($roles as $role)
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="roles" value="{{ $role->name }}" id="edit_role_{{ $role->id }}" 
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <label for="edit_role_{{ $role->id }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $role->name }}</label>
                            </div>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" wire:model="status" id="edit_status" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <x-input-label for="edit_status" :value="__('Active')" class="ml-2" />
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
    
    <!-- Delete User Modal -->
    <x-modal wire:model="showDeleteModal" title="Delete User">
        <div class="space-y-4">
            <p>Are you sure you want to delete the user "{{ $selectedUser->name }}"?</p>
            <p class="text-red-600 dark:text-red-400">This action will move the user to trash. You can restore them later if needed.</p>
        </div>
        
        <div class="mt-6 flex justify-end">
            <x-secondary-button wire:click="$toggle('showDeleteModal')" :disabled="false">
                {{ __('Cancel') }}
            </x-secondary-button>
            
            <x-danger-button class="ml-3" wire:click="deleteUser">
                {{ __('Delete') }}
            </x-danger-button>
        </div>
    </x-modal>
    
    <!-- Bulk Action Modal -->
    <x-modal wire:model="showBulkActionModal" title="Confirm Bulk Action">
        <div class="space-y-4">
            <p>Are you sure you want to {{ $bulkAction }} {{ count($selectedUsers) }} user{{ count($selectedUsers) > 1 ? 's' : '' }}?</p>
            @if ($bulkAction === 'delete')
                <p class="text-red-600 dark:text-red-400">This action will move the selected users to trash. You can restore them later if needed.</p>
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
    <x-modal wire:model="showRestoreModal" title="Restore User">
        <div class="space-y-4">
            <p>Are you sure you want to restore the selected user?</p>
        </div>
        
        <div class="mt-6 flex justify-end">
            <x-secondary-button wire:click="$toggle('showRestoreModal')" :disabled="false">
                {{ __('Cancel') }}
            </x-secondary-button>
            
            <x-primary-button class="ml-3" wire:click="restoreUser({{ $selectedUser->id }})">
                {{ __('Restore') }}
            </x-primary-button>
        </div>
    </x-modal>
</div>