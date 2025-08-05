<?php
// app/Modules/DocumentManagement/Livewire/DocumentManagement.php
namespace App\Modules\DocumentManagement\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Services\DocumentService;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;

class DocumentManagement extends Component
{
    use WithPagination, WithFileUploads;
    
    public $model;
    public $modelId;
    public $modelType;
    public $documents;
    public $showUploadModal = false;
    public $showEditModal = false;
    public $showViewModal = false;
    public $showVerifyModal = false;
    public $selectedDocument = null;
    
    // Form fields
    public $title = '';
    public $type = '';
    public $description = '';
    public $tags = [];
    public $expires_at = '';
    public $is_public = false;
    public $file;
    public $verification_notes = '';
    
    protected $documentService;
    
    protected $rules = [
        'title' => 'required|string|max:255',
        'type' => 'required|string|max:100',
        'file' => 'required|file|max:10240', // max 10MB
        'tags' => 'array',
        'expires_at' => 'nullable|date',
        'is_public' => 'boolean',
        'description' => 'nullable|string|max:1000',
    ];
    
    public function boot(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }
    
    public function mount($modelId, $modelType)
    {
        $this->modelId = $modelId;
        $this->modelType = $modelType;
        
        // Get the model instance
        $this->model = $modelType::findOrFail($modelId);
        
        // Load documents for this model
        $this->loadDocuments();
    }
    
    public function loadDocuments()
    {
        $this->documents = $this->model->documents()->with('creator')->latest()->get();
    }
    
    public function openUploadModal()
    {
        $this->reset(['title', 'type', 'description', 'tags', 'expires_at', 'is_public', 'file']);
        $this->showUploadModal = true;
    }
    
    public function uploadDocument()
    {
        $this->validate();
        
        try {
            $this->documentService->uploadDocument($this->model, [
                'title' => $this->title,
                'type' => $this->type,
                'file' => $this->file,
                'tags' => $this->tags,
                'expires_at' => $this->expires_at,
                'is_public' => $this->is_public,
                'description' => $this->description,
            ]);
            
            $this->showUploadModal = false;
            $this->loadDocuments();
            
            flash()->success('Document uploaded successfully!');
        } catch (\Exception $e) {
            flash()->error('Error uploading document: ' . $e->getMessage());
        }
    }
    
    public function openEditModal($documentId)
    {
        $this->selectedDocument = Document::find($documentId);
        
        $this->title = $this->selectedDocument->title;
        $this->type = $this->selectedDocument->type;
        $this->description = $this->selectedDocument->description;
        $this->tags = $this->selectedDocument->tags;
        $this->expires_at = $this->selectedDocument->expires_at;
        $this->is_public = $this->selectedDocument->is_public;
        
        $this->showEditModal = true;
    }
    
    public function updateDocument()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'file' => 'nullable|file|max:10240',
            'tags' => 'array',
            'expires_at' => 'nullable|date',
            'is_public' => 'boolean',
            'description' => 'nullable|string|max:1000',
        ]);
        
        try {
            $updateData = [
                'title' => $this->title,
                'type' => $this->type,
                'tags' => $this->tags,
                'expires_at' => $this->expires_at,
                'is_public' => $this->is_public,
                'description' => $this->description,
            ];
            
            if ($this->file) {
                $updateData['file'] = $this->file;
            }
            
            $this->documentService->updateDocument($this->selectedDocument, $updateData);
            
            $this->showEditModal = false;
            $this->loadDocuments();
            
            flash()->success('Document updated successfully!');
        } catch (\Exception $e) {
            flash()->error('Error updating document: ' . $e->getMessage());
        }
    }
    
    public function openViewModal($documentId)
    {
        $this->selectedDocument = Document::with('creator')->find($documentId);
        $this->showViewModal = true;
    }
    
    public function openVerifyModal($documentId)
    {
        $this->selectedDocument = Document::find($documentId);
        $this->verification_notes = '';
        $this->showVerifyModal = true;
    }
    
    public function verifyDocument($status)
    {
        try {
            $this->documentService->verifyDocument($this->selectedDocument, $status, $this->verification_notes);
            
            $this->showVerifyModal = false;
            $this->loadDocuments();
            
            $message = $status === 'verified' ? 'Document verified successfully!' : 'Document rejected.';
            flash()->success($message);
        } catch (\Exception $e) {
            flash()->error('Error verifying document: ' . $e->getMessage());
        }
    }
    
    public function deleteDocument($documentId)
    {
        try {
            $document = Document::find($documentId);
            $this->documentService->deleteDocument($document);
            
            $this->loadDocuments();
            
            flash()->success('Document deleted successfully!');
        } catch (\Exception $e) {
            flash()->error('Error deleting document: ' . $e->getMessage());
        }
    }
    
    public function downloadDocument($documentId)
    {
        $document = Document::find($documentId);
        
        if (!$document) {
            flash()->error('Document not found!');
            return;
        }
        
        $path = storage_path('app/public/' . $document->file_path);
        
        if (!file_exists($path)) {
            flash()->error('File not found!');
            return;
        }
        
        return response()->download($path, $document->file_name);
    }
    
    public function render()
    {
        return view('documentmanagement::livewire.document-management', [
            'documents' => $this->documents,
        ]);
    }
}