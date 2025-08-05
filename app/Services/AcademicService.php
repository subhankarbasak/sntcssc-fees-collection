<?php

// app/Services/AcademicService.php
namespace App\Services;

use App\Models\Programme;
use App\Models\Batch;
use App\Models\Section;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use App\Events\ProgrammeCreated;
use App\Events\BatchCreated;
use App\Events\SectionCreated;

class AcademicService
{
    public function getAllProgrammes()
    {
        return Programme::with('batches')->get();
    }
    
    public function createProgramme(array $data)
    {
        try {
            DB::beginTransaction();
            
            $programme = Programme::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'duration_months' => $data['duration_months'],
            ]);
            
            DB::commit();
            
            ProgrammeCreated::dispatch($programme);
            
            return $programme;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function updateProgramme(Programme $programme, array $data)
    {
        try {
            DB::beginTransaction();
            
            $programme->update([
                'name' => $data['name'] ?? $programme->name,
                'description' => $data['description'] ?? $programme->description,
                'duration_months' => $data['duration_months'] ?? $programme->duration_months,
            ]);
            
            DB::commit();
            
            return $programme;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function deleteProgramme(Programme $programme)
    {
        try {
            DB::beginTransaction();
            
            $programme->delete();
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function getAllBatches($filters = [])
    {
        $query = Batch::with('programme');
        
        if (!empty($filters['programme_id'])) {
            $query->where('programme_id', $filters['programme_id']);
        }
        
        return $query->get();
    }
    
    public function createBatch(array $data)
    {
        try {
            DB::beginTransaction();
            
            $batch = Batch::create([
                'programme_id' => $data['programme_id'],
                'name' => $data['name'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
            ]);
            
            DB::commit();
            
            BatchCreated::dispatch($batch);
            
            return $batch;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function updateBatch(Batch $batch, array $data)
    {
        try {
            DB::beginTransaction();
            
            $batch->update([
                'name' => $data['name'] ?? $batch->name,
                'start_date' => $data['start_date'] ?? $batch->start_date,
                'end_date' => $data['end_date'] ?? $batch->end_date,
            ]);
            
            DB::commit();
            
            return $batch;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function deleteBatch(Batch $batch)
    {
        try {
            DB::beginTransaction();
            
            $batch->delete();
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function getAllSections($filters = [])
    {
        $query = Section::with('batch.programme');
        
        if (!empty($filters['batch_id'])) {
            $query->where('batch_id', $filters['batch_id']);
        }
        
        return $query->get();
    }
    
    public function createSection(array $data)
    {
        try {
            DB::beginTransaction();
            
            $section = Section::create([
                'batch_id' => $data['batch_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);
            
            DB::commit();
            
            SectionCreated::dispatch($section);
            
            return $section;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function updateSection(Section $section, array $data)
    {
        try {
            DB::beginTransaction();
            
            $section->update([
                'name' => $data['name'] ?? $section->name,
                'description' => $data['description'] ?? $section->description,
            ]);
            
            DB::commit();
            
            return $section;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function deleteSection(Section $section)
    {
        try {
            DB::beginTransaction();
            
            $section->delete();
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function getEnrollments($filters = [])
    {
        $query = Enrollment::with('student.user', 'batch.programme', 'section');
        
        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }
        
        if (!empty($filters['batch_id'])) {
            $query->where('batch_id', $filters['batch_id']);
        }
        
        if (!empty($filters['section_id'])) {
            $query->where('section_id', $filters['section_id']);
        }
        
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        return $query->get();
    }
    
    public function updateEnrollmentStatus(Enrollment $enrollment, $status)
    {
        try {
            DB::beginTransaction();
            
            $enrollment->update([
                'status' => $status,
            ]);
            
            DB::commit();
            
            return $enrollment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}