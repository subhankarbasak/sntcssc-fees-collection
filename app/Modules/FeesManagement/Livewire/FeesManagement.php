<?php

// app/Modules/FeesManagement/Livewire/FeeManagement.php
namespace App\Modules\FeesManagement\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\FeeService;
use App\Models\FeeType;
use App\Models\FeeStructure;
use App\Models\StudentFee;
use App\Models\Batch;
use App\Models\Programme;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class FeesManagement extends Component
{
    use WithPagination;
    
    public $search = '';
    public $filterProgramme = '';
    public $filterBatch = '';
    public $filterFeeType = '';
    public $filterStatus = '';
    public $filterCategory = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $showBulkActionModal = false;
    public $showRestoreModal = false;
    public $selectedFee = null;
    public $selectedFees = [];
    public $selectAll = false;
    public $bulkAction = '';
    
    // Form fields
    public $title = '';
    public $description = '';
    public $frequency = 'one_time';
    public $is_refundable = false;
    public $selectedProgramme = '';
    public $selectedBatch = '';
    public $amount = '';
    public $category = 'Unreserved';
    
    protected $feeService;
    protected $queryString = [
        'search' => ['except' => ''],
        'filterProgramme' => ['except' => ''],
        'filterBatch' => ['except' => ''],
        'filterFeeType' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterCategory' => ['except' => ''],
        'filterDateFrom' => ['except' => ''],
        'filterDateTo' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];
    
    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'frequency' => 'required|in:monthly,one_time',
        'is_refundable' => 'boolean',
        'selectedProgramme' => 'required|exists:programmes,id',
        'selectedBatch' => 'required|exists:batches,id',
        'amount' => 'required|numeric|min:0',
        'category' => 'required|in:Unreserved,OBC,SC,ST',
    ];
    
    public function boot(FeeService $feeService)
    {
        $this->feeService = $feeService;
    }
    
    public function render()
    {
        $query = FeeStructure::with('feeType', 'batch.programme');
        
        // Apply search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('amount', 'like', '%' . $this->search . '%')
                  ->orWhereHas('feeType', function ($feeTypeQuery) {
                      $feeTypeQuery->where('name', 'like', '%' . $this->search . '%')
                                    ->orWhere('description', 'like', '%' . $this->search . '%');
                  });
            });
        }
        
        // Apply filters
        if (!empty($this->filterProgramme)) {
            $query->whereHas('batch.programme', function ($q) {
                $q->where('id', $this->filterProgramme);
            });
        }
        
        if (!empty($this->filterBatch)) {
            $query->where('batch_id', $this->filterBatch);
        }
        
        if (!empty($this->filterFeeType)) {
            $query->where('fee_type_id', $this->filterFeeType);
        }
        
        if (!empty($this->filterCategory)) {
            $query->where('category', $this->filterCategory);
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
        
        $feeStructures = $query->paginate(10);
        $feeTypes = FeeType::all();
        $programmes = Programme::all();
        $batches = Batch::with('programme')->get();
        
        return view('feesmanagement::livewire.fee-management', [
            'feeStructures' => $feeStructures,
            'feeTypes' => $feeTypes,
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
    
    public function updatingFilterFeeType()
    {
        $this->resetPage();
    }
    
    public function updatingFilterCategory()
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
            'search', 'filterProgramme', 'filterBatch', 'filterFeeType', 
            'filterCategory', 'filterDateFrom', 'filterDateTo', 'sortBy', 'sortDirection'
        ]);
    }
    
    public function openCreateModal()
    {
        $this->reset(['title', 'description', 'frequency', 'is_refundable', 'selectedProgramme', 'selectedBatch', 'amount', 'category']);
        $this->showCreateModal = true;
    }
    
    public function createFee()
    {
        $this->validate();
        
        try {
            // First, create or get the fee type
            $feeType = FeeType::firstOrCreate([
                'name' => $this->title,
                'description' => $this->description,
                'frequency' => $this->frequency,
                'is_refundable' => $this->is_refundable,
            ]);
            
            // Then create the fee structure
            $feeStructure = FeeStructure::create([
                'programme_id' => $this->selectedProgramme,
                'batch_id' => $this->selectedBatch,
                'fee_type_id' => $feeType->id,
                'amount' => $this->amount,
                'category' => $this->category,
            ]);
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'model_type' => FeeStructure::class,
                'model_id' => $feeStructure->id,
                'description' => "Created fee structure: {$feeType->name} for {$feeStructure->batch->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showCreateModal = false;
            
            flash()->success('Fee structure created successfully!');
        } catch (\Exception $e) {
            flash()->error('Error creating fee structure: ' . $e->getMessage());
        }
    }
    
    public function openEditModal($feeId)
    {
        $feeStructure = FeeStructure::withTrashed()->find($feeId);
        $this->selectedFee = $feeStructure;
        
        $this->title = $feeStructure->feeType->name;
        $this->description = $feeStructure->feeType->description;
        $this->frequency = $feeStructure->feeType->frequency;
        $this->is_refundable = $feeStructure->feeType->is_refundable;
        $this->selectedProgramme = $feeStructure->programme_id;
        $this->selectedBatch = $feeStructure->batch_id;
        $this->amount = $feeStructure->amount;
        $this->category = $feeStructure->category;
        
        $this->showEditModal = true;
    }
    
    public function updateFee()
    {
        $this->validate();
        
        try {
            $feeStructure = $this->selectedFee;
            
            // Update the fee type
            $feeStructure->feeType->update([
                'name' => $this->title,
                'description' => $this->description,
                'frequency' => $this->frequency,
                'is_refundable' => $this->is_refundable,
            ]);
            
            // Update the fee structure
            $feeStructure->update([
                'programme_id' => $this->selectedProgramme,
                'batch_id' => $this->selectedBatch,
                'amount' => $this->amount,
                'category' => $this->category,
            ]);
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'update',
                'model_type' => FeeStructure::class,
                'model_id' => $feeStructure->id,
                'description' => "Updated fee structure: {$feeStructure->feeType->name} for {$feeStructure->batch->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showEditModal = false;
            
            flash()->success('Fee structure updated successfully!');
        } catch (\Exception $e) {
            flash()->error('Error updating fee structure: ' . $e->getMessage());
        }
    }
    
    public function openDeleteModal($feeId)
    {
        $this->selectedFee = FeeStructure::find($feeId);
        $this->showDeleteModal = true;
    }
    
    public function deleteFee()
    {
        try {
            $this->selectedFee->delete();
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'delete',
                'model_type' => FeeStructure::class,
                'model_id' => $this->selectedFee->id,
                'description' => "Deleted fee structure: {$this->selectedFee->feeType->name} for {$this->selectedFee->batch->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showDeleteModal = false;
            
            flash()->success('Fee structure deleted successfully!');
        } catch (\Exception $e) {
            flash()->error('Error deleting fee structure: ' . $e->getMessage());
        }
    }
    
    public function forceDeleteFee($feeId)
    {
        try {
            $feeStructure = FeeStructure::withTrashed()->find($feeId);
            $feeStructure->forceDelete();
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'force_delete',
                'model_type' => FeeStructure::class,
                'model_id' => $feeStructure->id,
                'description' => "Permanently deleted fee structure: {$feeStructure->feeType->name} for {$feeStructure->batch->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            flash()->success('Fee structure permanently deleted!');
        } catch (\Exception $e) {
            flash()->error('Error deleting fee structure: ' . $e->getMessage());
        }
    }
    
    public function restoreFee($feeId)
    {
        try {
            $feeStructure = FeeStructure::withTrashed()->find($feeId);
            $feeStructure->restore();
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'restore',
                'model_type' => FeeStructure::class,
                'model_id' => $feeStructure->id,
                'description' => "Restored fee structure: {$feeStructure->feeType->name} for {$feeStructure->batch->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showRestoreModal = false;
            
            flash()->success('Fee structure restored successfully!');
        } catch (\Exception $e) {
            flash()->error('Error restoring fee structure: ' . $e->getMessage());
        }
    }
    
    public function openBulkActionModal($action)
    {
        if (empty($this->selectedFees)) {
            flash()->error('Please select at least one fee structure.');
            return;
        }
        
        $this->bulkAction = $action;
        $this->showBulkActionModal = true;
    }
    
    public function performBulkAction()
    {
        try {
            $feeStructures = FeeStructure::whereIn('id', $this->selectedFees)->get();
            
            foreach ($feeStructures as $feeStructure) {
                switch ($this->bulkAction) {
                    case 'delete':
                        $feeStructure->delete();
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
                'model_type' => FeeStructure::class,
                'model_id' => null,
                'description' => "Performed bulk action: {$this->bulkAction} on " . count($feeStructures) . " fee structures",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showBulkActionModal = false;
            $this->selectedFees = [];
            $this->selectAll = false;
            
            flash()->success("Bulk action completed successfully!");
        } catch (\Exception $e) {
            flash()->error('Error performing bulk action: ' . $e->getMessage());
        }
    }
    
    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedFees = $this->feeStructures->pluck('id')->toArray();
        } else {
            $this->selectedFees = [];
        }
    }
    
    public function exportFees()
    {
        try {
            $feeStructures = FeeStructure::whereIn('id', $this->selectedFees)
                ->with('feeType', 'batch.programme')
                ->get();
            
            // Log the export action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'export',
                'model_type' => FeeStructure::class,
                'model_id' => null,
                'description' => "Exported " . count($feeStructures) . " fee structures",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            return response()->streamDownload(function () use ($feeStructures) {
                $csv = fopen('php://output', 'w');
                
                // Header
                fputcsv($csv, ['ID', 'Fee Type', 'Description', 'Frequency', 'Programme', 'Batch', 'Amount', 'Category', 'Refundable', 'Created At']);
                
                // Data
                foreach ($feeStructures as $feeStructure) {
                    fputcsv($csv, [
                        $feeStructure->id,
                        $feeStructure->feeType->name,
                        $feeStructure->feeType->description,
                        $feeStructure->feeType->frequency,
                        $feeStructure->batch->programme->name,
                        $feeStructure->batch->name,
                        $feeStructure->amount,
                        $feeStructure->category,
                        $feeStructure->feeType->is_refundable ? 'Yes' : 'No',
                        $feeStructure->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                
                fclose($csv);
            }, 'fee_structures_export_' . date('Y_m_d_His') . '.csv');
        } catch (\Exception $e) {
            flash()->error('Error exporting fee structures: ' . $e->getMessage());
        }
    }
    
    public function getBatchesForProgramme($programmeId)
    {
        if (empty($programmeId)) {
            return [];
        }
        
        return Batch::where('programme_id', $programmeId)->get();
    }
}