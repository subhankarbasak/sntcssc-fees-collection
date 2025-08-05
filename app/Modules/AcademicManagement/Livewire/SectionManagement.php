<?php

// app/Modules/AcademicManagement/Livewire/SectionManagement.php

namespace App\Modules\AcademicManagement\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Section;
use App\Models\Batch;
use App\Models\Programme;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;


class SectionManagement extends Component
{
    use WithPagination;
    
    public $search = '';
    public $filterProgramme = '';
    public $filterBatch = '';
    public $filterStatus = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $showModal = false;
    public $showDeleteModal = false;
    public $showBulkActionModal = false;
    public $isEdit = false;
    public $selectedSection = null;
    public $selectedSections = [];
    public $selectAll = false;
    public $bulkActionType = '';
    
    // Form fields
    public $name = '';
    public $description = '';
    public $selectedBatch = '';
    public $status = true;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'filterProgramme' => ['except' => ''],
        'filterBatch' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'selectedBatch' => 'required|exists:batches,id',
        'status' => 'boolean',
    ];
    
    public function render()
    {
        $query = Section::with(['batch.programme']);
        
        // Apply search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }
        
        // Apply filters
        if (!empty($this->filterProgramme)) {
            $query->whereHas('batch', function ($q) {
                $q->where('programme_id', $this->filterProgramme);
            });
        }
        
        if (!empty($this->filterBatch)) {
            $query->where('batch_id', $this->filterBatch);
        }
        
        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus === 'active');
        }
        
        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);
        
        // Include trashed if we're showing soft deleted items
        if (request()->routeIs('*.trashed')) {
            $query->onlyTrashed();
        }
        
        $sections = $query->paginate(10);
        $programmes = Programme::all();
        $batches = Batch::all();
        
        return view('academicmanagement::livewire.section-management', [
            'sections' => $sections,
            'programmes' => $programmes,
            'batches' => $batches,
        ]);
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingFilterProgramme()
    {
        $this->resetPage();
    }
    
    public function updatingFilterBatch()
    {
        $this->resetPage();
    }
    
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
    
    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }
    
    public function resetFilters()
    {
        $this->reset([
            'search', 'filterProgramme', 'filterBatch', 'filterStatus', 
            'sortBy', 'sortDirection'
        ]);
    }
    
    public function openCreateModal()
    {
        $this->reset(['name', 'description', 'selectedBatch', 'status']);
        $this->isEdit = false;
        $this->showModal = true;
    }
    
    public function create()
    {
        $this->validate();
        
        try {
            $section = Section::create([
                'name' => $this->name,
                'description' => $this->description,
                'batch_id' => $this->selectedBatch,
                'status' => $this->status,
            ]);
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'model_type' => Section::class,
                'model_id' => $section->id,
                'description' => "Created section: {$section->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showModal = false;
            
            flash()->success('Section created successfully!');
        } catch (\Exception $e) {
            flash()->error('Error creating section: ' . $e->getMessage());
        }
    }
    
    public function edit($sectionId)
    {
        $section = Section::withTrashed()->find($sectionId);
        $this->selectedSection = $section;
        
        $this->name = $section->name;
        $this->description = $section->description;
        $this->selectedBatch = $section->batch_id;
        $this->status = $section->status;
        
        $this->isEdit = true;
        $this->showModal = true;
    }
    
    public function update()
    {
        $this->validate();
        
        try {
            $this->selectedSection->update([
                'name' => $this->name,
                'description' => $this->description,
                'batch_id' => $this->selectedBatch,
                'status' => $this->status,
            ]);
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'update',
                'model_type' => Section::class,
                'model_id' => $this->selectedSection->id,
                'description' => "Updated section: {$this->selectedSection->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showModal = false;
            
            flash()->success('Section updated successfully!');
        } catch (\Exception $e) {
            flash()->error('Error updating section: ' . $e->getMessage());
        }
    }
    
    public function delete($sectionId)
    {
        $this->selectedSection = Section::find($sectionId);
        $this->showDeleteModal = true;
    }
    
    public function confirmDelete()
    {
        try {
            $this->selectedSection->delete();
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'delete',
                'model_type' => Section::class,
                'model_id' => $this->selectedSection->id,
                'description' => "Deleted section: {$this->selectedSection->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showDeleteModal = false;
            
            flash()->success('Section deleted successfully!');
        } catch (\Exception $e) {
            flash()->error('Error deleting section: ' . $e->getMessage());
        }
    }
    
    public function forceDelete($sectionId)
    {
        try {
            $section = Section::withTrashed()->find($sectionId);
            $section->forceDelete();
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'force_delete',
                'model_type' => Section::class,
                'model_id' => $section->id,
                'description' => "Permanently deleted section: {$section->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            flash()->success('Section permanently deleted!');
        } catch (\Exception $e) {
            flash()->error('Error deleting section: ' . $e->getMessage());
        }
    }
    
    public function restore($sectionId)
    {
        try {
            $section = Section::withTrashed()->find($sectionId);
            $section->restore();
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'restore',
                'model_type' => Section::class,
                'model_id' => $section->id,
                'description' => "Restored section: {$section->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            flash()->success('Section restored successfully!');
        } catch (\Exception $e) {
            flash()->error('Error restoring section: ' . $e->getMessage());
        }
    }
    
    public function openBulkActionModal($action)
    {
        if (empty($this->selectedSections)) {
            flash()->error('Please select at least one section.');
            return;
        }
        
        $this->bulkActionType = $action;
        $this->showBulkActionModal = true;
    }
    
    public function confirmBulkAction()
    {
        try {
            $sections = Section::whereIn('id', $this->selectedSections)->get();
            
            foreach ($sections as $section) {
                switch ($this->bulkActionType) {
                    case 'delete':
                        $section->delete();
                        break;
                    case 'export':
                        // This will be handled separately
                        break;
                }
            }
            
            // Log the bulk action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'bulk_' . $this->bulkActionType,
                'model_type' => Section::class,
                'model_id' => null,
                'description' => "Performed bulk action: {$this->bulkActionType} on " . count($sections) . " sections",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showBulkActionModal = false;
            $this->selectedSections = [];
            $this->selectAll = false;
            
            flash()->success("Bulk action completed successfully!");
        } catch (\Exception $e) {
            flash()->error('Error performing bulk action: ' . $e->getMessage());
        }
    }
    
    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedSections = $this->sections->pluck('id')->toArray();
        } else {
            $this->selectedSections = [];
        }
    }
    
    public function exportSections()
    {
        try {
            $sections = Section::whereIn('id', $this->selectedSections)
                ->with(['batch.programme'])
                ->get();
            
            // Log the export action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'export',
                'model_type' => Section::class,
                'model_id' => null,
                'description' => "Exported " . count($sections) . " sections",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            return response()->streamDownload(function () use ($sections) {
                $csv = fopen('php://output', 'w');
                
                // Header
                fputcsv($csv, ['ID', 'Name', 'Batch', 'Programme', 'Description', 'Status', 'Created At']);
                
                // Data
                foreach ($sections as $section) {
                    fputcsv($csv, [
                        $section->id,
                        $section->name,
                        $section->batch->name,
                        $section->batch->programme->name,
                        $section->description,
                        $section->status ? 'Active' : 'Inactive',
                        $section->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                
                fclose($csv);
            }, 'sections_export_' . date('Y_m_d_His') . '.csv');
        } catch (\Exception $e) {
            flash()->error('Error exporting sections: ' . $e->getMessage());
        }
    }
    
    public function closeModal()
    {
        $this->showModal = false;
    }
    
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
    }
    
    public function closeBulkActionModal()
    {
        $this->showBulkActionModal = false;
    }
}