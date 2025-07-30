<?php

namespace App\Livewire\Students;

use Livewire\Component;
use App\Models\Student;
use App\Models\AuditLog;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class StudentManager extends Component
{
    use WithPagination;

    public $form = [
        'application_number' => '',
        'admission_test_roll_no' => '',
        'programme_name' => '',
        'batch' => '',
        'student_id' => '',
        'section' => '',
        'first_name' => '',
        'last_name' => '',
        'district' => '',
        'address' => '',
        'category' => 'Unreserved',
        'dob' => '',
        'gender' => '',
        'email' => '',
        'alternate_email' => '',
        'mobile' => '',
        'alternate_mobile' => '',
        'whatsapp' => '',
        'is_pwbd' => false,
        'occupation' => '',
        'father_name' => '',
        'mother_name' => '',
        'father_occupation' => '',
        'mother_occupation' => '',
        'family_income' => '',
        'selection_type' => '',
        'score_A' => '',
        'score_B' => '',
        'score_C' => '',
        'score_D' => '',
        'status' => '',
        'note' => '',
        'remarks' => '',
    ];

    public $quickEdit = [
        'id' => null,
        'status' => '',
        'email' => '',
    ];

    public $editingId = null;
    public $search = '';
    public $perPage = 10;
    public $sortField = 'student_id';
    public $sortDirection = 'asc';
    public $filters = [
        'category' => '',
        'status' => '',
        'programme_name' => '',
        'batch' => '',
    ];
    public $selectedStudents = [];
    public $showDeleted = false;
    public $loading = false;
    public $gotoPage = 1;
    public $showFilters = false;
    public $exporting = false;
    public $showModal = false;
    public $modalId = null;

    protected $rules = [
        'form.application_number' => 'required|unique:students,application_number',
        'form.admission_test_roll_no' => 'nullable|unique:students,admission_test_roll_no',
        'form.programme_name' => 'required|string|max:255',
        'form.batch' => 'required|string|max:10',
        'form.student_id' => 'required|unique:students,student_id',
        'form.section' => 'nullable|string|max:10',
        'form.first_name' => 'required|string|max:255',
        'form.last_name' => 'required|string|max:255',
        'form.district' => 'nullable|string|max:255',
        'form.address' => 'nullable|string|max:500',
        'form.category' => 'required|in:Unreserved,SC,ST,OBC',
        'form.dob' => 'nullable|date|before:today',
        'form.gender' => 'nullable|in:Male,Female,Other',
        'form.email' => 'required|email|unique:students,email',
        'form.alternate_email' => 'nullable|email|unique:students,alternate_email',
        'form.mobile' => 'nullable|regex:/^[0-9]{10}$/',
        'form.alternate_mobile' => 'nullable|regex:/^[0-9]{10}$/',
        'form.whatsapp' => 'nullable|regex:/^[0-9]{10}$/',
        'form.is_pwbd' => 'boolean',
        'form.occupation' => 'nullable|string|max:255',
        'form.father_name' => 'nullable|string|max:255',
        'form.mother_name' => 'nullable|string|max:255',
        'form.father_occupation' => 'nullable|string|max:255',
        'form.mother_occupation' => 'nullable|string|max:255',
        'form.family_income' => 'nullable|numeric|min:0',
        'form.selection_type' => 'nullable|string|max:255',
        'form.score_A' => 'nullable|numeric|min:0|max:100',
        'form.score_B' => 'nullable|numeric|min:0|max:100',
        'form.score_C' => 'nullable|numeric|min:0|max:100',
        'form.score_D' => 'nullable|numeric|min:0|max:100',
        'form.status' => 'nullable|string|max:255',
        'form.note' => 'nullable|string|max:1000',
        'form.remarks' => 'nullable|string|max:1000',
        'quickEdit.status' => 'nullable|string|max:255',
        'quickEdit.email' => 'required|email',
    ];

    protected $messages = [
        'form.application_number.required' => 'Application number is required.',
        'form.application_number.unique' => 'This application number is already taken.',
        'form.programme_name.required' => 'Programme name is required.',
        'form.batch.required' => 'Batch is required.',
        'form.student_id.required' => 'Student ID is required.',
        'form.student_id.unique' => 'This student ID is already taken.',
        'form.first_name.required' => 'First name is required.',
        'form.last_name.required' => 'Last name is required.',
        'form.email.required' => 'Email is required.',
        'form.email.email' => 'Please enter a valid email address.',
        'form.email.unique' => 'This email is already registered.',
        'form.alternate_email.email' => 'Please enter a valid alternate email address.',
        'form.alternate_email.unique' => 'This alternate email is already registered.',
        'form.mobile.regex' => 'Mobile number must be 10 digits.',
        'form.alternate_mobile.regex' => 'Alternate mobile number must be 10 digits.',
        'form.whatsapp.regex' => 'WhatsApp number must be 10 digits.',
        'form.dob.before' => 'Date of birth must be a valid date before today.',
        'form.family_income.numeric' => 'Family income must be a number.',
        'form.score_A.numeric' => 'Score A must be a number between 0 and 100.',
        'form.score_B.numeric' => 'Score B must be a number between 0 and 100.',
        'form.score_C.numeric' => 'Score C must be a number between 0 and 100.',
        'form.score_D.numeric' => 'Score D must be a number between 0 and 100.',
        'quickEdit.email.required' => 'Quick edit email is required.',
        'quickEdit.email.email' => 'Please enter a valid quick edit email address.',
        'quickEdit.email.unique' => 'This quick edit email is already registered.',
    ];

    public function mount()
    {
        $this->resetForm();
        $this->filters = Session::get('student_filters', $this->filters);
        $this->search = Session::get('student_search', '');
        $this->showDeleted = Session::get('student_show_deleted', false);
        $this->perPage = Session::get('student_per_page', 10);
    }

    public function updated($propertyName)
    {
        if (str_starts_with($propertyName, 'filters.') || $propertyName === 'search' || $propertyName === 'showDeleted') {
            Session::put('student_filters', $this->filters);
            Session::put('student_search', $this->search);
            Session::put('student_show_deleted', $this->showDeleted);
            $this->resetPage();
        } elseif ($propertyName === 'perPage') {
            Session::put('student_per_page', $this->perPage);
            $this->resetPage();
        }
    }

    public function resetForm()
    {
        $this->form = array_fill_keys(array_keys($this->form), '');
        $this->form['category'] = 'Unreserved';
        $this->form['is_pwbd'] = false;
        $this->editingId = null;
        $this->quickEdit = ['id' => null, 'status' => '', 'email' => ''];
        $this->showModal = false;
        $this->modalId = null;
        $this->resetErrorBag();
    }

    public function openModal($id = null)
    {
        $this->resetErrorBag();
        $this->quickEdit = ['id' => null, 'status' => '', 'email' => '']; // Reset quickEdit
        if ($id) {
            $student = Student::withTrashed()->findOrFail($id);
            $this->editingId = $id;
            $this->form = $student->toArray();
            $this->form['dob'] = $student->dob ? $student->dob->format('Y-m-d') : '';
        } else {
            $this->resetForm();
        }
        $this->showModal = true;
        $this->dispatch('open-modal', id: 'student-form');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->modalId = null;
        $this->resetForm();
        $this->resetErrorBag();
        $this->dispatch('close-modal', id: 'student-form');
    }

    public function save()
    {
        $this->loading = true;
        $this->resetErrorBag();
        Log::info('StudentManager::save called', ['editingId' => $this->editingId, 'form' => $this->form]);

        // Define rules for form only
        $formRules = array_filter($this->rules, fn($key) => str_starts_with($key, 'form.'), ARRAY_FILTER_USE_KEY);

        // Update unique rules for editing mode
        if ($this->editingId) {
            $formRules['form.application_number'] = 'required|unique:students,application_number,' . $this->editingId;
            $formRules['form.admission_test_roll_no'] = 'nullable|unique:students,admission_test_roll_no,' . $this->editingId;
            $formRules['form.student_id'] = 'required|unique:students,student_id,' . $this->editingId;
            $formRules['form.email'] = 'required|email|unique:students,email,' . $this->editingId;
            $formRules['form.alternate_email'] = 'nullable|email|unique:students,alternate_email,' . $this->editingId;
        }

        try {
            // Validate only form fields
            $validatedData = $this->validate($formRules);
            Log::info('Validation passed', ['validatedData' => $validatedData]);

            DB::transaction(function () use ($validatedData) {
                $data = $this->form;
                $data['created_by'] = Auth::id() ?? 1;
                $data['updated_by'] = Auth::id() ?? 1;

                if ($this->editingId) {
                    $student = Student::withTrashed()->findOrFail($this->editingId);
                    $student->update($data);
                    AuditLog::create([
                        'user_id' => Auth::id() ?? 1,
                        'action' => 'update',
                        'model_type' => Student::class,
                        'model_id' => $student->id,
                        'data' => $data,
                    ]);
                    session()->flash('status', 'Student updated successfully.');
                } else {
                    $student = Student::create($data);
                    AuditLog::create([
                        'user_id' => Auth::id() ?? 1,
                        'action' => 'create',
                        'model_type' => Student::class,
                        'model_id' => $student->id,
                        'data' => $data,
                    ]);
                    session()->flash('status', 'Student created successfully.');
                }
            });

            $this->closeModal();
        } catch (ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors(), 'form' => $this->form]);
            $this->setErrorBag($e->errors());
            session()->flash('error', 'Please correct the validation errors below.');
        } catch (QueryException $e) {
            Log::error('Database error', ['message' => $e->getMessage(), 'form' => $this->form]);
            $errorMessage = 'Database error: ';
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $errorMessage .= 'A record with this application number, student ID, or email already exists.';
            } else {
                $errorMessage .= 'Could not save the student record. Please try again.';
            }
            session()->flash('error', $errorMessage);
        } catch (\Exception $e) {
            Log::error('Unexpected error in save', ['message' => $e->getMessage(), 'form' => $this->form]);
            session()->flash('error', 'An unexpected error occurred: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function openQuickEdit($id)
    {
        $this->resetErrorBag();
        $student = Student::withTrashed()->findOrFail($id);
        $this->quickEdit = [
            'id' => $id,
            'status' => $student->status ?? '',
            'email' => $student->email ?? '',
        ];
        $this->dispatch('open-quick-edit', id: $id);
    }

    public function saveQuickEdit()
    {
        $this->loading = true;
        $this->resetErrorBag();
        Log::info('StudentManager::saveQuickEdit called', ['quickEdit' => $this->quickEdit]);

        // Define rules for quick edit only
        $quickEditRules = [
            'quickEdit.status' => 'nullable|string|max:255',
            'quickEdit.email' => 'required|email|unique:students,email,' . ($this->quickEdit['id'] ?? 0),
        ];

        try {
            $this->validate($quickEditRules);
            Log::info('Quick edit validation passed', ['quickEdit' => $this->quickEdit]);

            DB::transaction(function () {
                $student = Student::withTrashed()->findOrFail($this->quickEdit['id']);
                $student->update([
                    'status' => $this->quickEdit['status'],
                    'email' => $this->quickEdit['email'],
                    'updated_by' => Auth::id() ?? 1,
                ]);
                AuditLog::create([
                    'user_id' => Auth::id() ?? 1,
                    'action' => 'quick_update',
                    'model_type' => Student::class,
                    'model_id' => $student->id,
                    'data' => $this->quickEdit,
                ]);
                session()->flash('status', 'Student updated successfully.');
                flash()->success('Student updated successfully.');
                $this->quickEdit = ['id' => null, 'status' => '', 'email' => ''];
                $this->dispatch('close-quick-edit', id: $student->id);
            });
        } catch (ValidationException $e) {
            Log::error('Quick edit validation failed', ['errors' => $e->errors()]);
            $this->setErrorBag($e->errors());
            session()->flash('error', 'Please correct the validation errors below.');
        } catch (QueryException $e) {
            Log::error('Quick edit database error', ['message' => $e->getMessage()]);
            session()->flash('error', 'Database error: Could not update the student record.');
        } catch (\Exception $e) {
            Log::error('Unexpected error in quick edit', ['message' => $e->getMessage()]);
            session()->flash('error', 'An unexpected error occurred: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function delete($id)
    {
        $this->loading = true;
        try {
            DB::transaction(function () use ($id) {
                $student = Student::findOrFail($id);
                $student->delete();
                AuditLog::create([
                    'user_id' => Auth::id() ?? 1,
                    'action' => 'delete',
                    'model_type' => Student::class,
                    'model_id' => $student->id,
                    'data' => $student->toArray(),
                ]);
                session()->flash('status', 'Student deleted successfully.');
                flash()->success('Student deleted successfully.');
                $this->selectedStudents = array_diff($this->selectedStudents, [$id]);
                $this->modalId = null;
                $this->dispatch('close-modal', id: 'confirm-deletion-' . $id);
            });
        } catch (\Exception $e) {
            Log::error('Delete error', ['message' => $e->getMessage(), 'id' => $id]);
            session()->flash('error', 'Failed to delete student: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function bulkDelete()
    {
        $this->loading = true;
        try {
            DB::transaction(function () {
                $students = Student::whereIn('id', $this->selectedStudents)->get();
                foreach ($students as $student) {
                    $student->delete();
                    AuditLog::create([
                        'user_id' => Auth::id() ?? 1,
                        'action' => 'delete',
                        'model_type' => Student::class,
                        'model_id' => $student->id,
                        'data' => $student->toArray(),
                    ]);
                }
                session()->flash('status', 'Selected students deleted successfully.');
                $this->selectedStudents = [];
                $this->modalId = null;
                $this->dispatch('close-modal', id: 'confirm-bulk-deletion');
            });
        } catch (\Exception $e) {
            Log::error('Bulk delete error', ['message' => $e->getMessage()]);
            session()->flash('error', 'Failed to delete students: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function bulkRestore()
    {
        $this->loading = true;
        try {
            DB::transaction(function () {
                $students = Student::withTrashed()->whereIn('id', $this->selectedStudents)->get();
                foreach ($students as $student) {
                    $student->restore();
                    AuditLog::create([
                        'user_id' => Auth::id() ?? 1,
                        'action' => 'restore',
                        'model_type' => Student::class,
                        'model_id' => $student->id,
                        'data' => $student->toArray(),
                    ]);
                }
                session()->flash('status', 'Selected students restored successfully.');
                $this->selectedStudents = [];
                $this->modalId = null;
                $this->dispatch('close-modal', id: 'confirm-bulk-restore');
            });
        } catch (\Exception $e) {
            Log::error('Bulk restore error', ['message' => $e->getMessage()]);
            session()->flash('error', 'Failed to restore students: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function restore($id)
    {
        $this->loading = true;
        try {
            DB::transaction(function () use ($id) {
                $student = Student::withTrashed()->findOrFail($id);
                $student->restore();
                AuditLog::create([
                    'user_id' => Auth::id() ?? 1,
                    'action' => 'restore',
                    'model_type' => Student::class,
                    'model_id' => $student->id,
                    'data' => $student->toArray(),
                ]);
                session()->flash('status', 'Student restored successfully.');
                flash()->success('Student restored successfully.');
                $this->modalId = null;
                $this->dispatch('close-modal', id: 'confirm-restore-' . $id);
            });
        } catch (\Exception $e) {
            Log::error('Restore error', ['message' => $e->getMessage(), 'id' => $id]);
            session()->flash('error', 'Failed to restore student: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function export($selected = false)
    {
        $this->exporting = true;
        try {
            $export = new StudentsExport($this->filters, $this->search, $this->sortField, $this->sortDirection, $this->showDeleted, $selected ? $this->selectedStudents : []);
            return Excel::download($export, 'students_' . now()->format('Ymd_His') . '.csv');
        } catch (\Exception $e) {
            Log::error('Export error', ['message' => $e->getMessage()]);
            session()->flash('error', 'Failed to export students: ' . $e->getMessage());
        } finally {
            $this->exporting = false;
        }
    }

    public function updatedGotoPage()
    {
        $this->gotoPage = max(1, min($this->gotoPage, $this->students->lastPage()));
        $this->setPage($this->gotoPage);
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters()
    {
        $this->filters = array_fill_keys(array_keys($this->filters), '');
        $this->search = '';
        $this->showDeleted = false;
        Session::put('student_filters', $this->filters);
        Session::put('student_search', $this->search);
        Session::put('student_show_deleted', $this->showDeleted);
        $this->resetPage();
    }

    public function render()
    {
        $query = $this->showDeleted ? Student::withTrashed() : Student::query();

        $query->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('student_id', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        })
        ->when($this->filters['category'], fn ($query) => $query->where('category', $this->filters['category']))
        ->when($this->filters['status'], fn ($query) => $query->where('status', $this->filters['status']))
        ->when($this->filters['programme_name'], fn ($query) => $query->where('programme_name', 'like', '%' . $this->filters['programme_name'] . '%'))
        ->when($this->filters['batch'], fn ($query) => $query->where('batch', $this->filters['batch']))
        ->orderBy($this->sortField, $this->sortDirection);

        $students = $query->paginate($this->perPage);
        $this->students = $students;

        return view('livewire.students.student-manager', [
            'students' => $students,
            'categories' => ['Unreserved', 'SC', 'ST', 'OBC'],
            'statuses' => Student::pluck('status')->unique()->filter()->values()->toArray(),
            'programmes' => Student::pluck('programme_name')->unique()->filter()->values()->toArray(),
            'batches' => Student::pluck('batch')->unique()->filter()->values()->toArray(),
        ])->layout('components.layouts.app', ['title' => __('Student Management')]);
    }
}