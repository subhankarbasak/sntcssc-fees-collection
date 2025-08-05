<?php

// app/Modules/StudentManagement/Livewire/StudentManagement.php
namespace App\Modules\StudentManagement\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\StudentService;
use App\Models\Student;
use App\Models\Batch;
use App\Models\Programme;
use App\Models\AuditLog;

class StudentManagement extends Component
{
    use WithPagination;
    
    public $search = '';
    public $filterBatch = '';
    public $filterProgramme = '';
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
    public $showEnrollModal = false;
    public $selectedStudent = null;
    public $selectedStudents = [];
    public $selectAll = false;
    public $bulkAction = '';
    
    // Form fields
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $student_id = '';
    public $dob = '';
    public $category = 'Unreserved';
    public $phone = '';
    public $address = '';
    public $selectedBatch = '';
    public $selectedSection = '';
    
    protected $studentService;
    protected $queryString = [
        'search' => ['except' => ''],
        'filterBatch' => ['except' => ''],
        'filterProgramme' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterCategory' => ['except' => ''],
        'filterDateFrom' => ['except' => ''],
        'filterDateTo' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
        'student_id' => 'required|string|unique:students,student_id',
        'dob' => 'required|date',
        'category' => 'required|in:Unreserved,OBC,SC,ST',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
    ];
    
    public function boot(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }
    
    public function render()
    {
        $query = Student::with('user', 'enrollments.batch.programme');
        
        // Apply search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('student_id', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                  });
            });
        }
        
        // Apply filters
        if (!empty($this->filterBatch)) {
            $query->whereHas('enrollments', function ($q) {
                $q->where('batch_id', $this->filterBatch);
            });
        }
        
        if (!empty($this->filterProgramme)) {
            $query->whereHas('enrollments.batch.programme', function ($q) {
                $q->where('id', $this->filterProgramme);
            });
        }
        
        if (!empty($this->filterStatus)) {
            $query->whereHas('enrollments', function ($q) {
                $q->where('status', $this->filterStatus);
            });
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
        
        $students = $query->paginate(10);
        $batches = Batch::with('programme')->get();
        $programmes = Programme::all();
        
        return view('modules.studentmanagement.livewire.student-management', [
            'students' => $students,
            'batches' => $batches,
            'programmes' => $programmes,
        ]);
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingFilterBatch()
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
            'search', 'filterBatch', 'filterProgramme', 'filterStatus', 
            'filterCategory', 'filterDateFrom', 'filterDateTo', 'sortBy', 'sortDirection'
        ]);
    }
    
    public function openCreateModal()
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'student_id', 'dob', 'category', 'phone', 'address']);
        $this->showCreateModal = true;
    }
    
    public function createStudent()
    {
        $this->validate();
        
        try {
            $student = $this->studentService->createStudent([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'student_id' => $this->student_id,
                'dob' => $this->dob,
                'category' => $this->category,
                'phone' => $this->phone,
                'address' => $this->address,
            ]);
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'model_type' => Student::class,
                'model_id' => $student->id,
                'description' => "Created student: {$student->student_id}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showCreateModal = false;
            
            flash()->success('Student created successfully!');
        } catch (\Exception $e) {
            flash()->error('Error creating student: ' . $e->getMessage());
        }
    }
    
    public function openEditModal($studentId)
    {
        $student = Student::withTrashed()->find($studentId);
        $this->selectedStudent = $student;
        $this->name = $student->user->name;
        $this->email = $student->user->email;
        $this->student_id = $student->student_id;
        $this->dob = $student->dob->format('Y-m-d');
        $this->category = $student->category;
        $this->phone = $student->profile->phone ?? '';
        $this->address = $student->profile->address ?? '';
        
        $this->showEditModal = true;
    }
    
    public function updateStudent()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->selectedStudent->user_id,
            'student_id' => 'required|string|unique:students,student_id,' . $this->selectedStudent->id,
            'dob' => 'required|date',
            'category' => 'required|in:Unreserved,OBC,SC,ST',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);
        
        try {
            $userData = [
                'name' => $this->name,
                'email' => $this->email,
            ];
            
            $studentData = [
                'student_id' => $this->student_id,
                'dob' => $this->dob,
                'category' => $this->category,
            ];
            
            $profileData = [
                'phone' => $this->phone,
                'address' => $this->address,
            ];
            
            $this->studentService->updateStudent($this->selectedStudent, $userData, $studentData, $profileData);
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'update',
                'model_type' => Student::class,
                'model_id' => $this->selectedStudent->id,
                'description' => "Updated student: {$this->selectedStudent->student_id}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showEditModal = false;
            
            flash()->success('Student updated successfully!');
        } catch (\Exception $e) {
            flash()->error('Error updating student: ' . $e->getMessage());
        }
    }
    
    public function openDeleteModal($studentId)
    {
        $this->selectedStudent = Student::find($studentId);
        $this->showDeleteModal = true;
    }
    
    public function deleteStudent()
    {
        try {
            $this->studentService->deleteStudent($this->selectedStudent);
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'delete',
                'model_type' => Student::class,
                'model_id' => $this->selectedStudent->id,
                'description' => "Deleted student: {$this->selectedStudent->student_id}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showDeleteModal = false;
            
            flash()->success('Student deleted successfully!');
        } catch (\Exception $e) {
            flash()->error('Error deleting student: ' . $e->getMessage());
        }
    }
    
    public function forceDeleteStudent($studentId)
    {
        try {
            $student = Student::withTrashed()->find($studentId);
            $student->forceDelete();
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'force_delete',
                'model_type' => Student::class,
                'model_id' => $student->id,
                'description' => "Permanently deleted student: {$student->student_id}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            flash()->success('Student permanently deleted!');
        } catch (\Exception $e) {
            flash()->error('Error deleting student: ' . $e->getMessage());
        }
    }
    
    public function restoreStudent($studentId)
    {
        try {
            $student = Student::withTrashed()->find($studentId);
            $student->restore();
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'restore',
                'model_type' => Student::class,
                'model_id' => $student->id,
                'description' => "Restored student: {$student->student_id}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showRestoreModal = false;
            
            flash()->success('Student restored successfully!');
        } catch (\Exception $e) {
            flash()->error('Error restoring student: ' . $e->getMessage());
        }
    }
    
    public function openEnrollModal($studentId)
    {
        $this->selectedStudent = Student::find($studentId);
        $this->selectedBatch = '';
        $this->selectedSection = '';
        $this->showEnrollModal = true;
    }
    
    public function enrollStudent()
    {
        $this->validate([
            'selectedBatch' => 'required|exists:batches,id',
            'selectedSection' => 'nullable|exists:sections,id',
        ]);
        
        try {
            $batch = Batch::find($this->selectedBatch);
            
            // Check if student is already enrolled in this batch
            $existingEnrollment = $this->selectedStudent->enrollments()
                ->where('batch_id', $this->selectedBatch)
                ->first();
            
            if ($existingEnrollment) {
                flash()->error('Student is already enrolled in this batch.');
                return;
            }
            
            $enrollmentData = [
                'batch_id' => $this->selectedBatch,
                'section_id' => $this->selectedSection,
                'status' => 'active',
                'enrollment_date' => now(),
            ];
            
            $this->studentService->enrollStudent($this->selectedStudent, $enrollmentData);
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'enroll',
                'model_type' => Student::class,
                'model_id' => $this->selectedStudent->id,
                'description' => "Enrolled student {$this->selectedStudent->student_id} in batch {$batch->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showEnrollModal = false;
            
            flash()->success('Student enrolled successfully!');
        } catch (\Exception $e) {
            flash()->error('Error enrolling student: ' . $e->getMessage());
        }
    }
    
    public function openBulkActionModal($action)
    {
        if (empty($this->selectedStudents)) {
            flash()->error('Please select at least one student.');
            return;
        }
        
        $this->bulkAction = $action;
        $this->showBulkActionModal = true;
    }
    
    public function performBulkAction()
    {
        try {
            $students = Student::whereIn('id', $this->selectedStudents)->get();
            
            foreach ($students as $student) {
                switch ($this->bulkAction) {
                    case 'delete':
                        $student->delete();
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
                'model_type' => Student::class,
                'model_id' => null,
                'description' => "Performed bulk action: {$this->bulkAction} on " . count($students) . " students",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showBulkActionModal = false;
            $this->selectedStudents = [];
            $this->selectAll = false;
            
            flash()->success("Bulk action completed successfully!");
        } catch (\Exception $e) {
            flash()->error('Error performing bulk action: ' . $e->getMessage());
        }
    }
    
    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedStudents = $this->students->pluck('id')->toArray();
        } else {
            $this->selectedStudents = [];
        }
    }
    
    public function exportStudents()
    {
        try {
            $students = Student::whereIn('id', $this->selectedStudents)->with('user')->get();
            
            // Log the export action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'export',
                'model_type' => Student::class,
                'model_id' => null,
                'description' => "Exported " . count($students) . " students",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            return response()->streamDownload(function () use ($students) {
                $csv = fopen('php://output', 'w');
                
                // Header
                fputcsv($csv, ['ID', 'Student ID', 'Name', 'Email', 'DOB', 'Category', 'Phone', 'Address', 'Created At']);
                
                // Data
                foreach ($students as $student) {
                    fputcsv($csv, [
                        $student->id,
                        $student->student_id,
                        $student->user->name,
                        $student->user->email,
                        $student->dob->format('Y-m-d'),
                        $student->category,
                        $student->profile->phone ?? '',
                        $student->profile->address ?? '',
                        $student->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                
                fclose($csv);
            }, 'students_export_' . date('Y_m_d_His') . '.csv');
        } catch (\Exception $e) {
            flash()->error('Error exporting students: ' . $e->getMessage());
        }
    }
    
    public function getSectionsForBatch($batchId)
    {
        if (empty($batchId)) {
            return [];
        }
        
        $batch = Batch::find($batchId);
        return $batch->sections;
    }
}