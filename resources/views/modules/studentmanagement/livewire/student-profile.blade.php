<!-- resources/views/modules/studentmanagement/livewire/student-profile.blade.php -->
<div>
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Student Profile</h2>
            <button wire:click="openEditModal" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Edit Profile
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="flex items-center mb-4">
                    <img src="{{ $user->getFirstMediaUrl('photos') ?: asset('images/default-avatar.png') }}" 
                         alt="Profile Photo" class="w-24 h-24 rounded-full object-cover mr-4">
                    <div>
                        <h3 class="text-xl font-semibold">{{ $user->name }}</h3>
                        <p class="text-gray-600">Student ID: {{ $student->student_id }}</p>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <p><span class="font-medium">Email:</span> {{ $user->email }}</p>
                    <p><span class="font-medium">Phone:</span> {{ $student->profile->phone ?: 'N/A' }}</p>
                    <p><span class="font-medium">Date of Birth:</span> {{ $student->dob ? $student->dob->format('d M Y') : 'N/A' }}</p>
                    <p><span class="font-medium">Category:</span> {{ $student->category }}</p>
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold mb-2">Address</h3>
                <p class="text-gray-700">{{ $student->profile->address ?: 'No address provided' }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Documents</h2>
            <button wire:click="toggleDocumentManagement" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                {{ $showDocumentManagement ? 'Hide Documents' : 'Manage Documents' }}
            </button>
        </div>
        
        @if ($showDocumentManagement)
            <livewire:document-management :modelId="$student->id" :modelType="'App\Models\Student'" />
        @endif
    </div>
    
    <!-- Edit Profile Modal -->
    <x-modal wire:model="showEditModal">
        <x-slot name="title">Edit Profile</x-slot>
        
        <form wire:submit.prevent="updateProfile">
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
                    <x-input-label for="phone" :value="__('Phone')" />
                    <x-text-input wire:model="phone" id="phone" class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="address" :value="__('Address')" />
                    <textarea wire:model="address" id="address" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="dob" :value="__('Date of Birth')" />
                    <x-text-input wire:model="dob" id="dob" class="block mt-1 w-full" type="date" />
                    <x-input-error :messages="$errors->get('dob')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="category" :value="__('Category')" />
                    <select wire:model="category" id="category" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        <option value="Unreserved">Unreserved</option>
                        <option value="OBC">OBC</option>
                        <option value="SC">SC</option>
                        <option value="ST">ST</option>
                    </select>
                    <x-input-error :messages="$errors->get('category')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="photo" :value="__('Profile Photo')" />
                    <input type="file" wire:model="photo" id="photo" class="block mt-1 w-full" />
                    <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                    <p class="mt-1 text-sm text-gray-500">Max file size: 2MB</p>
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
</div>