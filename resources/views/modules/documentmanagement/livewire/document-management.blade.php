<!-- resources/views/modules/documentmanagement/livewire/document-management.blade.php -->
<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Documents</h2>
        <button wire:click="openUploadModal" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Upload Document
        </button>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($documents as $document)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $document->title }}</div>
                                <div class="text-sm text-gray-500">{{ $document->file_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $document->type }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if ($document->verification_status === 'verified')
                                        bg-green-100 text-green-800
                                    @elseif ($document->verification_status === 'rejected')
                                        bg-red-100 text-red-800
                                    @elseif ($document->verification_status === 'under_review')
                                        bg-yellow-100 text-yellow-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $document->verification_status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $document->creator->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button wire:click="openViewModal({{ $document->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">View</button>
                                <button wire:click="openEditModal({{ $document->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                <button wire:click="downloadDocument({{ $document->id }})" class="text-green-600 hover:text-green-900 mr-3">Download</button>
                                @if (auth()->user()->can('documents.verify'))
                                    <button wire:click="openVerifyModal({{ $document->id }})" class="text-yellow-600 hover:text-yellow-900 mr-3">Verify</button>
                                @endif
                                <button wire:click="deleteDocument({{ $document->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                No documents found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Upload Modal -->
    <x-modal wire:model="showUploadModal">
        <x-slot name="title">Upload Document</x-slot>
        
        <form wire:submit.prevent="uploadDocument">
            <div class="space-y-4">
                <div>
                    <x-input-label for="title" :value="__('Title')" />
                    <x-text-input wire:model="title" id="title" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="type" :value="__('Type')" />
                    <select wire:model="type" id="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        <option value="">Select Type</option>
                        <option value="identity">Identity Proof</option>
                        <option value="cv">CV</option>
                        <option value="certificate">Certificate</option>
                        <option value="marksheet">Marksheet</option>
                        <option value="other">Other</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="file" :value="__('File')" />
                    <input type="file" wire:model="file" id="file" class="block mt-1 w-full" required />
                    <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    <p class="mt-1 text-sm text-gray-500">Max file size: 10MB</p>
                </div>
                
                <div>
                    <x-input-label for="description" :value="__('Description')" />
                    <textarea wire:model="description" id="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="tags" :value="__('Tags')" />
                    <input type="text" wire:model="tags" id="tags" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                    <p class="mt-1 text-sm text-gray-500">Separate tags with commas</p>
                    <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="expires_at" :value="__('Expires At')" />
                    <x-text-input wire:model="expires_at" id="expires_at" class="block mt-1 w-full" type="date" />
                    <x-input-error :messages="$errors->get('expires_at')" class="mt-2" />
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" wire:model="is_public" id="is_public" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <x-input-label for="is_public" :value="__('Public Document')" class="ml-2" />
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$toggle('showUploadModal')" :disabled="false">
                    {{ __('Cancel') }}
                </x-secondary-button>
                
                <x-primary-button class="ml-3" :disabled="{{ $errors->count() > 0 }}">
                    {{ __('Upload') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
    
    <!-- Edit Modal -->
    <x-modal wire:model="showEditModal">
        <x-slot name="title">Edit Document</x-slot>
        
        <form wire:submit.prevent="updateDocument">
            <div class="space-y-4">
                <div>
                    <x-input-label for="title" :value="__('Title')" />
                    <x-text-input wire:model="title" id="title" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="type" :value="__('Type')" />
                    <select wire:model="type" id="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        <option value="">Select Type</option>
                        <option value="identity">Identity Proof</option>
                        <option value="cv">CV</option>
                        <option value="certificate">Certificate</option>
                        <option value="marksheet">Marksheet</option>
                        <option value="other">Other</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="file" :value="__('Replace File (Optional)')" />
                    <input type="file" wire:model="file" id="file" class="block mt-1 w-full" />
                    <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    <p class="mt-1 text-sm text-gray-500">Max file size: 10MB</p>
                </div>
                
                <div>
                    <x-input-label for="description" :value="__('Description')" />
                    <textarea wire:model="description" id="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="tags" :value="__('Tags')" />
                    <input type="text" wire:model="tags" id="tags" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                    <p class="mt-1 text-sm text-gray-500">Separate tags with commas</p>
                    <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                </div>
                
                <div>
                    <x-input-label for="expires_at" :value="__('Expires At')" />
                    <x-text-input wire:model="expires_at" id="expires_at" class="block mt-1 w-full" type="date" />
                    <x-input-error :messages="$errors->get('expires_at')" class="mt-2" />
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" wire:model="is_public" id="is_public" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <x-input-label for="is_public" :value="__('Public Document')" class="ml-2" />
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
    
    <!-- View Modal -->
    <x-modal wire:model="showViewModal">
        <x-slot name="title">Document Details</x-slot>
        
        @if ($selectedDocument)
            <div class="space-y-4">
                <div>
                    <x-input-label :value="__('Title')" />
                    <p class="mt-1 text-sm text-gray-900">{{ $selectedDocument->title }}</p>
                </div>
                
                <div>
                    <x-input-label :value="__('Type')" />
                    <p class="mt-1 text-sm text-gray-900">{{ $selectedDocument->type }}</p>
                </div>
                
                <div>
                    <x-input-label :value="__('File Name')" />
                    <p class="mt-1 text-sm text-gray-900">{{ $selectedDocument->file_name }}</p>
                </div>
                
                <div>
                    <x-input-label :value="__('File Size')" />
                    <p class="mt-1 text-sm text-gray-900">{{ number_format($selectedDocument->size / 1024, 2) }} KB</p>
                </div>
                
                <div>
                    <x-input-label :value="__('MIME Type')" />
                    <p class="mt-1 text-sm text-gray-900">{{ $selectedDocument->mime_type }}</p>
                </div>
                
                <div>
                    <x-input-label :value="__('Description')" />
                    <p class="mt-1 text-sm text-gray-900">{{ $selectedDocument->description ?: 'N/A' }}</p>
                </div>
                
                <div>
                    <x-input-label :value="__('Tags')" />
                    <p class="mt-1 text-sm text-gray-900">{{ implode(', ', $selectedDocument->tags) ?: 'N/A' }}</p>
                </div>
                
                <div>
                    <x-input-label :value="__('Expires At')" />
                    <p class="mt-1 text-sm text-gray-900">{{ $selectedDocument->expires_at ? $selectedDocument->expires_at->format('d M Y') : 'N/A' }}</p>
                </div>
                
                <div>
                    <x-input-label :value="__('Public Document')" />
                    <p class="mt-1 text-sm text-gray-900">{{ $selectedDocument->is_public ? 'Yes' : 'No' }}</p>
                </div>
                
                <div>
                    <x-input-label :value="__('Verification Status')" />
                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $selectedDocument->verification_status)) }}</p>
                </div>
                
                <div>
                    <x-input-label :value="__('Uploaded By')" />
                    <p class="mt-1 text-sm text-gray-900">{{ $selectedDocument->creator->name }}</p>
                </div>
                
                <div>
                    <x-input-label :value="__('Uploaded At')" />
                    <p class="mt-1 text-sm text-gray-900">{{ $selectedDocument->uploaded_at ? $selectedDocument->uploaded_at->format('d M Y H:i') : 'N/A' }}</p>
                </div>
                
                <div>
                    <x-input-label :value="__('Verified At')" />
                    <p class="mt-1 text-sm text-gray-900">{{ $selectedDocument->verified_at ? $selectedDocument->verified_at->format('d M Y H:i') : 'N/A' }}</p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="downloadDocument({{ $selectedDocument->id }})" class="mr-3">
                    {{ __('Download') }}
                </x-secondary-button>
                
                <x-secondary-button wire:click="$toggle('showViewModal')" :disabled="false">
                    {{ __('Close') }}
                </x-secondary-button>
            </div>
        @endif
    </x-modal>
    
    <!-- Verify Modal -->
    <x-modal wire:model="showVerifyModal">
        <x-slot name="title">Verify Document</x-slot>
        
        @if ($selectedDocument)
            <div class="space-y-4">
                <div>
                    <x-input-label :value="__('Document Title')" />
                    <p class="mt-1 text-sm text-gray-900">{{ $selectedDocument->title }}</p>
                </div>
                
                <div>
                    <x-input-label for="verification_notes" :value="__('Notes')" />
                    <textarea wire:model="verification_notes" id="verification_notes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
                    <x-input-error :messages="$errors->get('verification_notes')" class="mt-2" />
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$toggle('showVerifyModal')" :disabled="false">
                    {{ __('Cancel') }}
                </x-secondary-button>
                
                <x-danger-button class="ml-3" wire:click="verifyDocument('rejected')">
                    {{ __('Reject') }}
                </x-danger-button>
                
                <x-primary-button class="ml-3" wire:click="verifyDocument('verified')">
                    {{ __('Verify') }}
                </x-primary-button>
            </div>
        @endif
    </x-modal>
</div>