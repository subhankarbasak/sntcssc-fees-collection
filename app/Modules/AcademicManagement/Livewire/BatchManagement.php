<?php

// app/Modules/AcademicManagement/Livewire/BatchManagement.php
namespace App\Modules\AcademicManagement\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Batch;
use App\Models\Programme;
use App\Models\AuditLog;

class BatchManagement extends Component
{
    use WithPagination;
    
    public $search = '';
    public $filterProgramme = '';
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
    public $selectedBatch = null;
    public $selectedBatches = [];
    public $selectAll = false;
    public $bulkAction = '';
    
    // Form fields
    public $name = '';
    public $start_date = '';
    public $end_date = '';
    public $description = '';
    public $selectedProgramme = '';
    public $status = true;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'filterProgramme' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterDateFrom' => ['except' => ''],
        'filterDateTo' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'description' => 'nullable|string|max:1000',
        'selectedProgramme' => 'required|exists:programmes,id',
        'status' => 'boolean',
    ];
    
    public function render()
    {
        $query = Batch::with('programme');
        
        // Apply search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }
        
        // Apply filters
        if (!empty($this->filterProgramme)) {
            $query->where('programme_id', $this->filterProgramme);
        }
        
        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus === 'active');
        }
        
        if (!empty($this->filterDateFrom)) {
            $query->whereDate('start_date', '>=', $this->filterDateFrom);
        }
        
        if (!empty($this->filterDateTo)) {
            $query->whereDate('end_date', '<=', $this->filterDateTo);
        }
        
        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);
        
        // Include trashed if we're showing soft deleted items
        if (request()->routeIs('*.trashed')) {
            $query->onlyTrashed();
        }
        
        $batches = $query->paginate(10);
        $programmes = Programme::all();
        
        return view('academicmanagement::livewire.batch-management', [
            'batches' => $batches,
            'programmes' => $programmes,
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
            'search', 'filterProgramme', 'filterStatus', 
            'filterDateFrom', 'filterDateTo', 'sortBy', 'sortDirection'
        ]);
    }
    
    public function openCreateModal()
    {
        $this->reset(['name', 'start_date', 'end_date', 'description', 'selectedProgramme', 'status']);
        $this->showCreateModal = true;
    }
    
    public function createBatch()
    {
        $this->validate();
        
        try {
            $batch = Batch::create([
                'name' => $this->name,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'description' => $this->description,
                'programme_id' => $this->selectedProgramme,
                'status' => $this->status,
            ]);
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'model_type' => Batch::class,
                'model_id' => $batch->id,
                'description' => "Created batch: {$batch->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showCreateModal = false;
            
            flash()->success('Batch created successfully!');
        } catch (\Exception $e) {
            flash()->error('Error creating batch: ' . $e->getMessage());
        }
    }
    
    public function openEditModal($batchId)
    {
        $batch = Batch::withTrashed()->find($batchId);
        $this->selectedBatch = $batch;
        
        $this->name = $batch->name;
        $this->start_date = $batch->start_date->format('Y-m-d');
        $this->end_date = $batch->end_date->format('Y-m-d');
        $this->description = $batch->description;
        $this->selectedProgramme = $batch->programme_id;
        $this->status = $batch->status;
        
        $this->showEditModal = true;
    }
    
    public function updateBatch()
    {
        $this->validate();
        
        try {
            $this->selectedBatch->update([
                'name' => $this->name,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'description' => $this->description,
                'programme_id' => $this->selectedProgramme,
                'status' => $this->status,
            ]);
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'update',
                'model_type' => Batch::class,
                'model_id' => $this->selectedBatch->id,
                'description' => "Updated batch: {$this->selectedBatch->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showEditModal = false;
            
            flash()->success('Batch updated successfully!');
        } catch (\Exception $e) {
            flash()->error('Error updating batch: ' . $e->getMessage());
        }
    }
    
    public function openDeleteModal($batchId)
    {
        $this->selectedBatch = Batch::find($batchId);
        $this->showDeleteModal = true;
    }
    
    public function deleteBatch()
    {
        try {
            $this->selectedBatch->delete();
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'delete',
                'model_type' => Batch::class,
                'model_id' => $this->selectedBatch->id,
                'description' => "Deleted batch: {$this->selectedBatch->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showDeleteModal = false;
            
            flash()->success('Batch deleted successfully!');
        } catch (\Exception $e) {
            flash()->error('Error deleting batch: ' . $e->getMessage());
        }
    }
    
    public function forceDeleteBatch($batchId)
    {
        try {
            $batch = Batch::withTrashed()->find($batchId);
            $batch->forceDelete();
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'force_delete',
                'model_type' => Batch::class,
                'model_id' => $batch->id,
                'description' => "Permanently deleted batch: {$batch->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            flash()->success('Batch permanently deleted!');
        } catch (\Exception $e) {
            flash()->error('Error deleting batch: ' . $e->getMessage());
        }
    }
    
    public function restoreBatch($batchId)
    {
        try {
            $batch = Batch::withTrashed()->find($batchId);
            $batch->restore();
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'restore',
                'model_type' => Batch::class,
                'model_id' => $batch->id,
                'description' => "Restored batch: {$batch->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showRestoreModal = false;
            
            flash()->success('Batch restored successfully!');
        } catch (\Exception $e) {
            flash()->error('Error restoring batch: ' . $e->getMessage());
        }
    }
    
    public function openBulkActionModal($action)
    {
        if (empty($this->selectedBatches)) {
            flash()->error('Please select at least one batch.');
            return;
        }
        
        $this->bulkAction = $action;
        $this->showBulkActionModal = true;
    }
    
    public function performBulkAction()
    {
        try {
            $batches = Batch::whereIn('id', $this->selectedBatches)->get();
            
            foreach ($batches as $batch) {
                switch ($this->bulkAction) {
                    case 'delete':
                        $batch->delete();
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
                'model_type' => Batch::class,
                'model_id' => null,
                'description' => "Performed bulk action: {$this->bulkAction} on " . count($batches) . " batches",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showBulkActionModal = false;
            $this->selectedBatches = [];
            $this->selectAll = false;
            
            flash()->success("Bulk action completed successfully!");
        } catch (\Exception $e) {
            flash()->error('Error performing bulk action: ' . $e->getMessage());
        }
    }
    
    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedBatches = $this->batches->pluck('id')->toArray();
        } else {
            $this->selectedBatches = [];
        }
    }
    
    public function exportBatches()
    {
        try {
            $batches = Batch::whereIn('id', $this->selectedBatches)
                ->with('programme')
                ->get();
            
            // Log the export action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'export',
                'model_type' => Batch::class,
                'model_id' => null,
                'description' => "Exported " . count($batches) . " batches",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            return response()->streamDownload(function () use ($batches) {
                $csv = fopen('php://output', 'w');
                
                // Header
                fputcsv($csv, ['ID', 'Name', 'Programme', 'Start Date', 'End Date', 'Description', 'Status', 'Created At']);
                
                // Data
                foreach ($batches as $batch) {
                    fputcsv($csv, [
                        $batch->id,
                        $batch->name,
                        $batch->programme->name,
                        $batch->start_date->format('Y-m-d'),
                        $batch->end_date->format('Y-m-d'),
                        $batch->description,
                        $batch->status ? 'Active' : 'Inactive',
                        $batch->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                
                fclose($csv);
            }, 'batches_export_' . date('Y_m_d_His') . '.csv');
        } catch (\Exception $e) {
            flash()->error('Error exporting batches: ' . $e->getMessage());
        }
    }
}