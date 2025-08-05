<!-- resources/views/modules/studentmanagement/livewire/student-management.blade.php -->
<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold">Student Management</h2>
        <div class="flex flex-wrap gap-2">
            <button wire:click="openCreateModal" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Add Student
            </button>
            @if (request()->routeIs('*.trashed'))
                <a href="{{ route(request()->route()->getName(), ['trashed' => false]) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    Active Students
                </a>
            @else
                <a href="{{ route(request()->route->getName() . '.trashed') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    Trashed Students
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
                    placeholder="Search students..." aria-label="Search students">
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
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="completed">Completed</option>
                    <option value="dropped">Dropped</option>
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
    @if (!empty($selectedStudents))
        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="font-medium text-blue-800 dark:text-blue-200">
                        {{ count($selectedStudents) }} student{{ count($selectedStudents) > 1 ? 's' : '' }} selected
                    </span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button wire:click="openBulkActionModal('delete')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                        Delete
                    </button>
                    <button wire:click="exportStudents" class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded text-sm">
                        Export
                    </button>
                    <button wire:click="$set('selectedStudents', [])" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm">
                        Clear Selection
                    </button>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Students Table -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" role="table" aria-label="Students table">
                <caption id="studentsTableDescription" class="sr-only">
                    A table showing students with their details and actions
                </caption>
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr role="row">
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" wire:model="selectAll" wire:change="toggleSelectAll" 
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500"
                                aria-label="Select all students">
                        </th>
                        <th wire:click="sortBy('student_id')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="flex items-center">
                                Student ID
                                @if ($sortBy === 'student_id')
                                    <span class="ml-1">
                                        @if ($sortDirection === 'asc') ↑ @else ↓ @endif
                                    </span>
                                @endif
                            </div>
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
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Category
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Enrollments
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
                    @forelse ($students as $student)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700" role="row">
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <input type="checkbox" wire:model="selectedStudents" value="{{ $student->id }}" 
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500"
                                    aria-label="Select student {{ $student->student_id }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->student_id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->user->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $student->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $student->category }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" role="cell">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    @foreach ($student->enrollments as $enrollment)
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">
                                            {{ $enrollment->batch->name }}
                                        </span>
                                    @endforeach
                                    @if ($student->enrollments->count() === 0)
                                        <span class="text-gray-500 dark:text-gray-400">No enrollments</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" role="cell">
                                {{ $student->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" role="cell">
                                <div role="group" aria-label="Student actions">
                                    <button wire:click="openEditModal({{ $student->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3" aria-label="Edit student {{ $student->student_id }}">Edit</button>
                                    <button wire:click="openEnrollModal({{ $student->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-3" aria-label="Enroll student {{ $student->student_id }}">Enroll</button>
                                    @if (request()->routeIs('*.trashed'))
                                        <button wire:click="restoreStudent({{ $student->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3" aria-label="Restore student {{ $student->student_id }}">Restore</button>
                                        <button wire:click="forceDeleteStudent({{ $student->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" aria-label="Delete permanently">Delete</button>
                                    @else
                                        <button wire:click="openDeleteModal({{ $student->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" aria-label="Delete student {{ $student->student_id }}">Delete</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr role="row">
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400" role="cell">
                                No students found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <button wire:click="previousPage" wire:disabled="$students->onFirstPage()" 
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Previous
                </button>
                <button wire:click="nextPage" wire:disabled="$students->onLastPage()" 
                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Next
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Showing <span class="font-medium">{{ $students->firstItem() }}</span> to <span class="font-medium">{{ $students->lastItem() }}</span> of <span class="font-medium">{{ $students->total() }}</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <button wire:click="previousPage" wire:disabled="$students->onFirstPage()" 
                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                            aria-label="Previous">
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <!-- Pagination Numbers -->
                        @foreach ($students->links()->elements[0] as $page)
                            @if ($page['url'])
                                <button wire:click="goToPage({{ $page['page'] }})" 
                                    class="{{ $students->currentPage() == $page['page'] ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-100' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600' }} relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    {{ $page['page'] }}
                                </button>
                            @else
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                                    {{ $page['page'] }}
                                </span>
                            @endif
                        @endforeach
                        
                        <button wire:click="nextPage" wire:disabled="$students->onLastPage()" 
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
        @forelse ($students as $student)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 mb-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $student->student_id }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $student->user->name }}</p>
                    </div>
                    <input type="checkbox" wire:model="selectedStudents" value="{{ $student->id }}" 
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500"
                        aria-label="Select student {{ $student->student_id }}">
                </div>
                
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Category</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->category }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Enrollments</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            @foreach ($student->enrollments as $enrollment)
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">
                                    {{ $enrollment->batch->name }}
                                </span>
                            @endforeach
                            @if ($student->enrollments->count() === 0)
                                <span class="text-gray-500 dark:text-gray-400">No enrollments</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Created At</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->created_at->format('d M Y') }}</p>
                    </div>
                </div>
                
                <div class="mt-4 flex flex-wrap gap-2">
                    <button wire:click="openEditModal({{ $student->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm">Edit</button>
                    <button wire:click="openEnrollModal({{ $student->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 text-sm">Enroll</button>
                    @if (request()->routeIs('*.trashed'))
                        <button wire:click="restoreStudent({{ $student->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm">Restore</button>
                        <button wire:click="forceDeleteStudent({{ $student->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm">Delete</button>
                    @else
                        <button wire:click="openDeleteModal({{ $student->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm">Delete</button>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 text-center">
                <p class="text-gray-500 dark:text-gray-400">No students found.</p>
            </div>
        @endforelse
    </div>
    
    <!-- Create Student Modal -->
    <x-modal wire:model="showCreateModal" title="Add Student">
        <form wire:submit.prevent="createStudent">
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
                    <x-input-label for="student_id" :value="__('Student ID')" />
                    <x-text-input wire:model="student_id" id="student_id" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="dob" :value="__('Date of Birth')" />
                    <x-text-input wire:model="dob" id="dob" class="block mt-1 w-full" type="date" required />
                    <x-input-error :messages="$errors->get('dob')" class="mt-2" />
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
                
                <div>
                    <x-input-label for="phone" :value="__('Phone')" />
                    <x-text-input wire:model="phone" id="phone" class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="address" :value="__('Address')" />
                    <textarea wire:model="address" id="address" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" rows="3"></textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
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
    
    <!-- Edit Student Modal -->
    <x-modal wire:model="showEditModal" title="Edit Student">
        <form wire:submit.prevent="updateStudent">
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
                    <x-input-label for="student_id" :value="__('Student ID')" />
                    <x-text-input wire:model="student_id" id="student_id" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="dob" :value="__('Date of Birth')" />
                    <x-text-input wire:model="dob" id="dob" class="block mt-1 w-full" type="date" required />
                    <x-input-error :messages="$errors->get('dob')" class="mt-2" />
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
                
                <div>
                    <x-input-label for="phone" :value="__('Phone')" />
                    <x-text-input wire:model="phone" id="phone" class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="address" :value="__('Address')" />
                    <textarea wire:model="address" id="address" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" rows="3"></textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
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
    
    <!-- Delete Student Modal -->
    <x-modal wire:model="showDeleteModal" title="Delete Student">
        <div class="space-y-4">
            <p>Are you sure you want to delete the student "{{ $selectedStudent->student_id }}"?</p>
            <p class="text-red-600 dark:text-red-400">This action will move the student to trash. You can restore them later if needed.</p>
        </div>
        
        <div class="mt-6 flex justify-end">
            <x-secondary-button wire:click="$toggle('showDeleteModal')" :disabled="false">
                {{ __('Cancel') }}
            </x-secondary-button>
            
            <x-danger-button class="ml-3" wire:click="deleteStudent">
                {{ __('Delete') }}
            </x-danger-button>
        </div>
    </x-modal>
    
    <!-- Enroll Student Modal -->
    <x-modal wire:model="showEnrollModal" title="Enroll Student">
        <form wire:submit.prevent="enrollStudent">
            <div class="space-y-4">
                <div>
                    <x-input-label for="selectedBatch" :value="__('Batch')" />
                    <select wire:model="selectedBatch" wire:change="$refresh" id="selectedBatch" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="">Select a batch</option>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch->id }}">{{ $batch->name }} ({{ $batch->programme->name }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('selectedBatch')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="selectedSection" :value="__('Section')" />
                    <select wire:model="selectedSection" id="selectedSection" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">No section</option>
                        @foreach ($this->getSectionsForBatch($selectedBatch) as $section)
                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('selectedSection')" class="mt-2" />
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$toggle('showEnrollModal')" :disabled="false">
                    {{ __('Cancel') }}
                </x-secondary-button>
                
                <x-primary-button class="ml-3" :disabled="{{ $errors->count() > 0 }}">
                    {{ __('Enroll') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
    
    <!-- Bulk Action Modal -->
    <x-modal wire:model="showBulkActionModal" title="Confirm Bulk Action">
        <div class="space-y-4">
            <p>Are you sure you want to {{ $bulkAction }} {{ count($selectedStudents) }} student{{ count($selectedStudents) > 1 ? 's' : '' }}?</p>
            @if ($bulkAction === 'delete')
                <p class="text-red-600 dark:text-red-400">This action will move the selected students to trash. You can restore them later if needed.</p>
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
    <x-modal wire:model="showRestoreModal" title="Restore Student">
        <div class="space-y-4">
            <p>Are you sure you want to restore the selected student?</p>
        </div>
        
        <div class="mt-6 flex justify-end">
            <x-secondary-button wire:click="$toggle('showRestoreModal')" :disabled="false">
                {{ __('Cancel') }}
            </x-secondary-button>
            
            <x-primary-button class="ml-3" wire:click="restoreStudent({{ $selectedStudent->id }})">
                {{ __('Restore') }}
            </x-primary-button>
        </div>
    </x-modal>
</div>