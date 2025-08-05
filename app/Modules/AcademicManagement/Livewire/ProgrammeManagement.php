<?php

// app/Modules/AcademicManagement/Livewire/ProgrammeManagement.php
namespace App\Modules\AcademicManagement\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Programme;
use App\Models\AuditLog;

class ProgrammeManagement extends Component
{
    use WithPagination;
    
    public $search = '';
    public $filterStatus = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $showBulkActionModal = false;
    public $showRestoreModal = false;
    public $selectedProgramme = null;
    public $selectedProgrammes = [];
    public $selectAll = false;
    public $bulkAction = '';
    
    // Form fields
    public $name = '';
    public $description = '';
    public $duration_months = '';
    public $status = true;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterDateFrom' => ['except' => ''],
        'filterDateTo' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'duration_months' => 'required|integer|min:1|max:60',
        'status' => 'boolean',
    ];
    
    public function render()
    {
        $query = Programme::query();
        
        // Apply search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }
        
        // Apply filters
        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus === 'active');
        }
        
        if (!empty($this->filterDateFrom)) {
            $query->whereDate('created_at', '>=', $this->filterDateFrom);
        }
        
        if (!empty($this->filterDateTo)) {
            $query->whereDate('created_at', '<=', $this->filterDateTo);
        }
        
        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);
        
        // Include trashed if we're showing soft deleted items
        if (request()->routeIs('*.trashed')) {
            $query->onlyTrashed();
        }
        
        $programmes = $query->paginate(10);
        
        return view('academicmanagement::livewire.programme-management', [
            'programmes' => $programmes,
        ]);
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
    
    public function updatingFilterDateFrom()
    {
        $this->resetPage();
    }
    
    public function updatingFilterDateTo()
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
            'search', 'filterStatus', 'filterDateFrom', 'filterDateTo', 'sortBy', 'sortDirection'
        ]);
    }
    
    public function openCreateModal()
    {
        $this->reset(['name', 'description', 'duration_months', 'status']);
        $this->showCreateModal = true;
    }
    
    public function createProgramme()
    {
        $this->validate();
        
        try {
            $programme = Programme::create([
                'name' => $this->name,
                'description' => $this->description,
                'duration_months' => $this->duration_months,
                'status' => $this->status,
            ]);
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'model_type' => Programme::class,
                'model_id' => $programme->id,
                'description' => "Created programme: {$programme->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showCreateModal = false;
            
            flash()->success('Programme created successfully!');
        } catch (\Exception $e) {
            flash()->error('Error creating programme: ' . $e->getMessage());
        }
    }
    
    public function openEditModal($programmeId)
    {
        $programme = Programme::withTrashed()->find($programmeId);
        $this->selectedProgramme = $programme;
        
        $this->name = $programme->name;
        $this->description = $programme->description;
        $this->duration_months = $programme->duration_months;
        $this->status = $programme->status;
        
        $this->showEditModal = true;
    }
    
    public function updateProgramme()
    {
        $this->validate();
        
        try {
            $this->selectedProgramme->update([
                'name' => $this->name,
                'description' => $this->description,
                'duration_months' => $this->duration_months,
                'status' => $this->status,
            ]);
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'update',
                'model_type' => Programme::class,
                'model_id' => $this->selectedProgramme->id,
                'description' => "Updated programme: {$this->selectedProgramme->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showEditModal = false;
            
            flash()->success('Programme updated successfully!');
        } catch (\Exception $e) {
            flash()->error('Error updating programme: ' . $e->getMessage());
        }
    }
    
    public function openDeleteModal($programmeId)
    {
        $this->selectedProgramme = Programme::find($programmeId);
        $this->showDeleteModal = true;
    }
    
    public function deleteProgramme()
    {
        try {
            $this->selectedProgramme->delete();
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'delete',
                'model_type' => Programme::class,
                'model_id' => $this->selectedProgramme->id,
                'description' => "Deleted programme: {$this->selectedProgramme->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showDeleteModal = false;
            
            flash()->success('Programme deleted successfully!');
        } catch (\Exception $e) {
            flash()->error('Error deleting programme: ' . $e->getMessage());
        }
    }
    
    public function forceDeleteProgramme($programmeId)
    {
        try {
            $programme = Programme::withTrashed()->find($programmeId);
            $programme->forceDelete();
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'force_delete',
                'model_type' => Programme::class,
                'model_id' => $programme->id,
                'description' => "Permanently deleted programme: {$programme->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            flash()->success('Programme permanently deleted!');
        } catch (\Exception $e) {
            flash()->error('Error deleting programme: ' . $e->getMessage());
        }
    }
    
    public function restoreProgramme($programmeId)
    {
        try {
            $programme = Programme::withTrashed()->find($programmeId);
            $programme->restore();
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'restore',
                'model_type' => Programme::class,
                'model_id' => $programme->id,
                'description' => "Restored programme: {$programme->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showRestoreModal = false;
            
            flash()->success('Programme restored successfully!');
        } catch (\Exception $e) {
            flash()->error('Error restoring programme: ' . $e->getMessage());
        }
    }
    
    public function openBulkActionModal($action)
    {
        if (empty($this->selectedProgrammes)) {
            flash()->error('Please select at least one programme.');
            return;
        }
        
        $this->bulkAction = $action;
        $this->showBulkActionModal = true;
    }
    
    public function performBulkAction()
    {
        try {
            $programmes = Programme::whereIn('id', $this->selectedProgrammes)->get();
            
            foreach ($programmes as $programme) {
                switch ($this->bulkAction) {
                    case 'delete':
                        $programme->delete();
                        break;
                    case 'export':
                        // This will be handled separately
                        break;
                }
            }
            
            // Log the bulk action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'bulk_' . $this->bulkAction,
                'model_type' => Programme::class,
                'model_id' => null,
                'description' => "Performed bulk action: {$this->bulkAction} on " . count($programmes) . " programmes",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showBulkActionModal = false;
            $this->selectedProgrammes = [];
            $this->selectAll = false;
            
            flash()->success("Bulk action completed successfully!");
        } catch (\Exception $e) {
            flash()->error('Error performing bulk action: ' . $e->getMessage());
        }
    }
    
    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedProgrammes = $this->programmes->pluck('id')->toArray();
        } else {
            $this->selectedProgrammes = [];
        }
    }
    
    public function exportProgrammes()
    {
        try {
            $programmes = Programme::whereIn('id', $this->selectedProgrammes)->get();
            
            // Log the export action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'export',
                'model_type' => Programme::class,
                'model_id' => null,
                'description' => "Exported " . count($programmes) . " programmes",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            return response()->streamDownload(function () use ($programmes) {
                $csv = fopen('php://output', 'w');
                
                // Header
                fputcsv($csv, ['ID', 'Name', 'Description', 'Duration (Months)', 'Status', 'Created At']);
                
                // Data
                foreach ($programmes as $programme) {
                    fputcsv($csv, [
                        $programme->id,
                        $programme->name,
                        $programme->description,
                        $programme->duration_months,
                        $programme->status ? 'Active' : 'Inactive',
                        $programme->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                
                fclose($csv);
            }, 'programmes_export_' . date('Y_m_d_His') . '.csv');
        } catch (\Exception $e) {
            flash()->error('Error exporting programmes: ' . $e->getMessage());
        }
    }
}