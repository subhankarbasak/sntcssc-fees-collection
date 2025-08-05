<?php

// app/Modules/StudentManagement/Livewire/StudentProfile.php
namespace App\Modules\StudentManagement\Livewire;

use Livewire\Component;
use App\Models\Student;
use App\Models\User;
use App\Services\StudentService;
use Illuminate\Support\Facades\Auth;

class StudentProfile extends Component
{
    public $student;
    public $user;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $dob;
    public $category;
    public $photo;
    public $showEditModal = false;
    public $showDocumentManagement = false;
    
    protected $studentService;
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . Auth::id(),
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
        'dob' => 'nullable|date',
        'category' => 'required|in:Unreserved,OBC,SC,ST',
        'photo' => 'nullable|image|max:2048', // max 2MB
    ];
    
    public function boot(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }
    
    public function mount()
    {
        $this->user = Auth::user();
        $this->student = $this->user->student;
        
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->phone = $this->student->profile->phone ?? null;
        $this->address = $this->student->profile->address ?? null;
        $this->dob = $this->student->dob;
        $this->category = $this->student->category;
    }
    
    public function openEditModal()
    {
        $this->showEditModal = true;
    }
    
    public function updateProfile()
    {
        $this->validate();
        
        try {
            $userData = [
                'name' => $this->name,
                'email' => $this->email,
            ];
            
            $studentData = [
                'dob' => $this->dob,
                'category' => $this->category,
            ];
            
            $profileData = [
                'phone' => $this->phone,
                'address' => $this->address,
            ];
            
            if ($this->photo) {
                $userData['photo'] = $this->photo;
            }
            
            $this->studentService->updateStudent($this->student, $userData, $studentData, $profileData);
            
            $this->showEditModal = false;
            
            flash()->success('Profile updated successfully!');
        } catch (\Exception $e) {
            flash()->error('Error updating profile: ' . $e->getMessage());
        }
    }
    
    public function toggleDocumentManagement()
    {
        $this->showDocumentManagement = !$this->showDocumentManagement;
    }
    
    public function render()
    {
        return view('studentmanagement::livewire.student-profile');
    }
}