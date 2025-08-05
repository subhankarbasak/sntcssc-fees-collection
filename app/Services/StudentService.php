<?php

// app/Services/StudentService.php
namespace App\Services;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Events\StudentCreated;
use App\Events\StudentUpdated;
use App\Events\StudentDeleted;

class StudentService
{
    public function getAllStudents($filters = [])
    {
        $query = Student::with('user', 'enrollments.batch.programme');
        
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('student_id', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('user', function ($userQuery) use ($filters) {
                      $userQuery->where('name', 'like', '%' . $filters['search'] . '%')
                                ->orWhere('email', 'like', '%' . $filters['search'] . '%');
                  });
            });
        }
        
        if (!empty($filters['batch_id'])) {
            $query->whereHas('enrollments', function ($q) use ($filters) {
                $q->where('batch_id', $filters['batch_id']);
            });
        }
        
        if (!empty($filters['programme_id'])) {
            $query->whereHas('enrollments.batch.programme', function ($q) use ($filters) {
                $q->where('id', $filters['programme_id']);
            });
        }
        
        return $query->paginate(10);
    }
    
    public function createStudent(array $data)
    {
        try {
            DB::beginTransaction();
            
            // Create user account for the student
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            
            // Assign student role
            $user->assignRole('student');
            
            // Create student profile
            $student = Student::create([
                'user_id' => $user->id,
                'student_id' => $data['student_id'],
                'dob' => $data['dob'],
                'category' => $data['category'],
            ]);
            
            if (isset($data['photo'])) {
                $user->addMedia($data['photo'])->toMediaCollection('photos');
            }
            
            DB::commit();
            
            StudentCreated::dispatch($student);
            
            return $student;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    // public function updateStudent(Student $student, array $data)
    // {
    //     try {
    //         DB::beginTransaction();
            
    //         $user = $student->user;
            
    //         $user->update([
    //             'name' => $data['name'] ?? $user->name,
    //             'email' => $data['email'] ?? $user->email,
    //         ]);
            
    //         if (isset($data['password'])) {
    //             $user->update([
    //                 'password' => Hash::make($data['password']),
    //             ]);
    //         }
            
    //         $student->update([
    //             'student_id' => $data['student_id'] ?? $student->student_id,
    //             'dob' => $data['dob'] ?? $student->dob,
    //             'category' => $data['category'] ?? $student->category,
    //         ]);
            
    //         if (isset($data['photo'])) {
    //             $user->clearMediaCollection('photos');
    //             $user->addMedia($data['photo'])->toMediaCollection('photos');
    //         }
            
    //         DB::commit();
            
    //         StudentUpdated::dispatch($student);
            
    //         return $student;
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         throw $e;
    //     }
    // }

    // app/Services/StudentService.php
    // Add this method to the StudentService class
    public function updateStudent(Student $student, array $userData, array $studentData, array $profileData = [])
    {
        try {
            DB::beginTransaction();
            
            // Update user data
            $student->user->update($userData);
            
            // Update student data
            $student->update($studentData);
            
            // Update or create student profile
            if (!empty($profileData)) {
                if ($student->profile) {
                    $student->profile->update($profileData);
                } else {
                    $student->profile()->create($profileData);
                }
            }
            
            DB::commit();
            
            return $student;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function deleteStudent(Student $student)
    {
        try {
            DB::beginTransaction();
            
            $student->delete();
            
            DB::commit();
            
            StudentDeleted::dispatch($student);
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function restoreStudent($id)
    {
        try {
            DB::beginTransaction();
            
            $student = Student::withTrashed()->findOrFail($id);
            $student->restore();
            
            DB::commit();
            
            return $student;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function enrollStudent(Student $student, array $enrollmentData)
    {
        try {
            DB::beginTransaction();
            
            $enrollment = $student->enrollments()->create([
                'batch_id' => $enrollmentData['batch_id'],
                'section_id' => $enrollmentData['section_id'] ?? null,
                'status' => $enrollmentData['status'] ?? 'active',
                'enrollment_date' => $enrollmentData['enrollment_date'] ?? now(),
            ]);
            
            // Create student fees based on the batch's fee structure
            $batch = \App\Models\Batch::find($enrollmentData['batch_id']);
            $feeStructures = $batch->feeStructures;
            
            foreach ($feeStructures as $feeStructure) {
                $dueDate = now();
                
                if ($feeStructure->feeType->frequency === 'monthly') {
                    // Create monthly fees for the duration of the programme
                    $programme = $batch->programme;
                    $durationMonths = $programme->duration_months;
                    
                    for ($i = 0; $i < $durationMonths; $i++) {
                        $student->fees()->create([
                            'fee_structure_id' => $feeStructure->id,
                            'due_date' => $dueDate->copy()->addMonths($i),
                            'amount' => $feeStructure->amount,
                            'status' => 'pending',
                        ]);
                    }
                } else {
                    // One-time fee
                    $student->fees()->create([
                        'fee_structure_id' => $feeStructure->id,
                        'due_date' => $dueDate,
                        'amount' => $feeStructure->amount,
                        'status' => 'pending',
                    ]);
                }
            }
            
            DB::commit();
            
            return $enrollment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
