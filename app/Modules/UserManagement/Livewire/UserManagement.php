<?php

// app/Modules/UserManagement/Livewire/UserManagement.php
namespace App\Modules\UserManagement\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\UserService;
use App\Models\User;
use App\Models\Role;
use App\Models\AuditLog;

class UserManagement extends Component
{
    use WithPagination;
    
    public $search = '';
    public $filterRole = '';
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
    public $selectedUser = null;
    public $selectedUsers = [];
    public $selectAll = false;
    public $bulkAction = '';
    
    // Form fields
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $roles = [];
    public $status = true;
    
    protected $userService;
    protected $queryString = [
        'search' => ['except' => ''],
        'filterRole' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterDateFrom' => ['except' => ''],
        'filterDateTo' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
        'roles' => 'required|array|min:1',
        'status' => 'boolean',
    ];
    
    public function boot(UserService $userService)
    {
        $this->userService = $userService;
    }
    
    public function render()
    {
        $query = User::query();
        
        // Apply search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }
        
        // Apply filters
        if (!empty($this->filterRole)) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', $this->filterRole);
            });
        }
        
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
        
        $users = $query->with('roles')->paginate(10);
        $roles = Role::all();
        
        return view('modules.usermanagement.livewire.user-management', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingFilterRole()
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
            'search', 'filterRole', 'filterStatus', 
            'filterDateFrom', 'filterDateTo', 'sortBy', 'sortDirection'
        ]);
    }
    
    public function openCreateModal()
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'roles', 'status']);
        $this->showCreateModal = true;
    }
    
    public function createUser()
    {
        $this->validate();
        
        try {
            $user = $this->userService->createUser([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'roles' => $this->roles,
                'status' => $this->status,
            ]);
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'model_type' => User::class,
                'model_id' => $user->id,
                'description' => "Created user: {$user->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showCreateModal = false;
            
            flash()->success('User created successfully!');
        } catch (\Exception $e) {
            flash()->error('Error creating user: ' . $e->getMessage());
        }
    }
    
    public function openEditModal($userId)
    {
        $user = User::withTrashed()->find($userId);
        $this->selectedUser = $user;
        
        $this->name = $user->name;
        $this->email = $user->email;
        $this->roles = $user->roles->pluck('name')->toArray();
        $this->status = $user->status;
        
        $this->showEditModal = true;
    }
    
    public function updateUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->selectedUser->id,
            'roles' => 'required|array|min:1',
            'status' => 'boolean',
        ]);
        
        try {
            $updateData = [
                'name' => $this->name,
                'email' => $this->email,
                'roles' => $this->roles,
                'status' => $this->status,
            ];
            
            if ($this->password) {
                $this->validate(['password' => 'min:8|confirmed']);
                $updateData['password'] = $this->password;
            }
            
            $this->userService->updateUser($this->selectedUser, $updateData);
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'update',
                'model_type' => User::class,
                'model_id' => $this->selectedUser->id,
                'description' => "Updated user: {$this->selectedUser->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showEditModal = false;
            
            flash()->success('User updated successfully!');
        } catch (\Exception $e) {
            flash()->error('Error updating user: ' . $e->getMessage());
        }
    }
    
    public function openDeleteModal($userId)
    {
        $this->selectedUser = User::find($userId);
        $this->showDeleteModal = true;
    }
    
    public function deleteUser()
    {
        try {
            $this->userService->deleteUser($this->selectedUser);
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'delete',
                'model_type' => User::class,
                'model_id' => $this->selectedUser->id,
                'description' => "Deleted user: {$this->selectedUser->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showDeleteModal = false;
            
            flash()->success('User deleted successfully!');
        } catch (\Exception $e) {
            flash()->error('Error deleting user: ' . $e->getMessage());
        }
    }
    
    public function forceDeleteUser($userId)
    {
        try {
            $user = User::withTrashed()->find($userId);
            $user->forceDelete();
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'force_delete',
                'model_type' => User::class,
                'model_id' => $user->id,
                'description' => "Permanently deleted user: {$user->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            flash()->success('User permanently deleted!');
        } catch (\Exception $e) {
            flash()->error('Error deleting user: ' . $e->getMessage());
        }
    }
    
    public function restoreUser($userId)
    {
        try {
            $user = User::withTrashed()->find($userId);
            $user->restore();
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'restore',
                'model_type' => User::class,
                'model_id' => $user->id,
                'description' => "Restored user: {$user->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showRestoreModal = false;
            
            flash()->success('User restored successfully!');
        } catch (\Exception $e) {
            flash()->error('Error restoring user: ' . $e->getMessage());
        }
    }
    
    public function openBulkActionModal($action)
    {
        if (empty($this->selectedUsers)) {
            flash()->error('Please select at least one user.');
            return;
        }
        
        $this->bulkAction = $action;
        $this->showBulkActionModal = true;
    }
    
    public function performBulkAction()
    {
        try {
            $users = User::whereIn('id', $this->selectedUsers)->get();
            
            foreach ($users as $user) {
                switch ($this->bulkAction) {
                    case 'delete':
                        $user->delete();
                        break;
                    case 'activate':
                        $user->update(['status' => true]);
                        break;
                    case 'deactivate':
                        $user->update(['status' => false]);
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
                'model_type' => User::class,
                'model_id' => null,
                'description' => "Performed bulk action: {$this->bulkAction} on " . count($users) . " users",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showBulkActionModal = false;
            $this->selectedUsers = [];
            $this->selectAll = false;
            
            flash()->success("Bulk action completed successfully!");
        } catch (\Exception $e) {
            flash()->error('Error performing bulk action: ' . $e->getMessage());
        }
    }
    
    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedUsers = $this->users->pluck('id')->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }
    
    public function exportUsers()
    {
        try {
            $users = User::whereIn('id', $this->selectedUsers)->get();
            
            // Log the export action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'export',
                'model_type' => User::class,
                'model_id' => null,
                'description' => "Exported " . count($users) . " users",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            return response()->streamDownload(function () use ($users) {
                $csv = fopen('php://output', 'w');
                
                // Header
                fputcsv($csv, ['ID', 'Name', 'Email', 'Status', 'Roles', 'Created At']);
                
                // Data
                foreach ($users as $user) {
                    fputcsv($csv, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->status ? 'Active' : 'Inactive',
                        $user->roles->pluck('name')->implode(', '),
                        $user->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                
                fclose($csv);
            }, 'users_export_' . date('Y_m_d_His') . '.csv');
        } catch (\Exception $e) {
            flash()->error('Error exporting users: ' . $e->getMessage());
        }
    }
}