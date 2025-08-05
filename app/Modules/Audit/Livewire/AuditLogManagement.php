<?php

// app/Modules/Audit/Livewire/AuditLogManagement.php
namespace App\Modules\Audit\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AuditLog;
use App\Models\User;

class AuditLogManagement extends Component
{
    use WithPagination;
    
    public $search = '';
    public $filterAction = '';
    public $filterModel = '';
    public $filterUser = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'filterAction' => ['except' => ''],
        'filterModel' => ['except' => ''],
        'filterUser' => ['except' => ''],
        'filterDateFrom' => ['except' => ''],
        'filterDateTo' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];
    
    public function render()
    {
        $query = AuditLog::with('user');
        
        // Apply search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                  ->orWhere('ip_address', 'like', '%' . $this->search . '%');
            });
        }
        
        // Apply filters
        if (!empty($this->filterAction)) {
            $query->where('action', $this->filterAction);
        }
        
        if (!empty($this->filterModel)) {
            $query->where('model_type', 'like', '%' . $this->filterModel . '%');
        }
        
        if (!empty($this->filterUser)) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->filterUser . '%');
            });
        }
        
        if (!empty($this->filterDateFrom)) {
            $query->whereDate('created_at', '>=', $this->filterDateFrom);
        }
        
        if (!empty($this->filterDateTo)) {
            $query->whereDate('created_at', '<=', $this->filterDateTo);
        }
        
        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);
        
        $auditLogs = $query->paginate(15);
        $users = User::all();
        
        return view('audit::livewire.audit-log-management', [
            'auditLogs' => $auditLogs,
            'users' => $users,
        ]);
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingFilterAction()
    {
        $this->resetPage();
    }
    
    public function updatingFilterModel()
    {
        $this->resetPage();
    }
    
    public function updatingFilterUser()
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
            'search', 'filterAction', 'filterModel', 'filterUser', 
            'filterDateFrom', 'filterDateTo', 'sortBy', 'sortDirection'
        ]);
    }
    
    public function exportAuditLogs()
    {
        try {
            $auditLogs = AuditLog::with('user')
                ->when($this->search, function ($query, $search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('description', 'like', '%' . $search . '%')
                          ->orWhere('ip_address', 'like', '%' . $search . '%');
                    });
                })
                ->when($this->filterAction, function ($query, $action) {
                    return $query->where('action', $action);
                })
                ->when($this->filterModel, function ($query, $model) {
                    return $query->where('model_type', 'like', '%' . $model . '%');
                })
                ->when($this->filterUser, function ($query, $user) {
                    return $query->whereHas('user', function ($q) use ($user) {
                        $q->where('name', 'like', '%' . $user . '%');
                    });
                })
                ->when($this->filterDateFrom, function ($query, $date) {
                    return $query->whereDate('created_at', '>=', $date);
                })
                ->when($this->filterDateTo, function ($query, $date) {
                    return $query->whereDate('created_at', '<=', $date);
                })
                ->get();
            
            return response()->streamDownload(function () use ($auditLogs) {
                $csv = fopen('php://output', 'w');
                
                // Header
                fputcsv($csv, ['ID', 'User', 'Action', 'Model Type', 'Model ID', 'Description', 'IP Address', 'User Agent', 'Created At']);
                
                // Data
                foreach ($auditLogs as $log) {
                    fputcsv($csv, [
                        $log->id,
                        $log->user ? $log->user->name : 'System',
                        $log->action,
                        $log->model_type,
                        $log->model_id,
                        $log->description,
                        $log->ip_address,
                        $log->user_agent,
                        $log->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                
                fclose($csv);
            }, 'audit_logs_export_' . date('Y_m_d_His') . '.csv');
        } catch (\Exception $e) {
            flash()->error('Error exporting audit logs: ' . $e->getMessage());
        }
    }
}