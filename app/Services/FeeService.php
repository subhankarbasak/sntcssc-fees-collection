<?php

// app/Services/FeeService.php
namespace App\Services;

use App\Models\FeeType;
use App\Models\FeeStructure;
use App\Models\StudentFee;
use Illuminate\Support\Facades\DB;
use App\Events\FeeCreated;
use App\Events\FeeUpdated;
use App\Events\FeeDeleted;

class FeeService
{
    public function getAllFeeTypes()
    {
        return FeeType::all();
    }
    
    public function createFeeType(array $data)
    {
        try {
            DB::beginTransaction();
            
            $feeType = FeeType::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'frequency' => $data['frequency'],
                'is_refundable' => $data['is_refundable'] ?? false,
            ]);
            
            DB::commit();
            
            FeeCreated::dispatch($feeType);
            
            return $feeType;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function updateFeeType(FeeType $feeType, array $data)
    {
        try {
            DB::beginTransaction();
            
            $feeType->update([
                'name' => $data['name'] ?? $feeType->name,
                'description' => $data['description'] ?? $feeType->description,
                'frequency' => $data['frequency'] ?? $feeType->frequency,
                'is_refundable' => $data['is_refundable'] ?? $feeType->is_refundable,
            ]);
            
            DB::commit();
            
            FeeUpdated::dispatch($feeType);
            
            return $feeType;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function deleteFeeType(FeeType $feeType)
    {
        try {
            DB::beginTransaction();
            
            $feeType->delete();
            
            DB::commit();
            
            FeeDeleted::dispatch($feeType);
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function getFeeStructures($filters = [])
    {
        $query = FeeStructure::with('feeType', 'programme', 'batch');
        
        if (!empty($filters['programme_id'])) {
            $query->where('programme_id', $filters['programme_id']);
        }
        
        if (!empty($filters['batch_id'])) {
            $query->where('batch_id', $filters['batch_id']);
        }
        
        return $query->get();
    }
    
    public function createFeeStructure(array $data)
    {
        try {
            DB::beginTransaction();
            
            $feeStructure = FeeStructure::create([
                'programme_id' => $data['programme_id'],
                'batch_id' => $data['batch_id'],
                'fee_type_id' => $data['fee_type_id'],
                'amount' => $data['amount'],
                'category' => $data['category'] ?? 'general',
            ]);
            
            DB::commit();
            
            return $feeStructure;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function updateFeeStructure(FeeStructure $feeStructure, array $data)
    {
        try {
            DB::beginTransaction();
            
            $feeStructure->update([
                'amount' => $data['amount'] ?? $feeStructure->amount,
                'category' => $data['category'] ?? $feeStructure->category,
            ]);
            
            DB::commit();
            
            return $feeStructure;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function deleteFeeStructure(FeeStructure $feeStructure)
    {
        try {
            DB::beginTransaction();
            
            $feeStructure->delete();
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function getStudentFees($studentId, $filters = [])
    {
        $query = StudentFee::with('feeStructure.feeType', 'feeStructure.batch.programme')
                           ->where('student_id', $studentId);
        
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (!empty($filters['batch_id'])) {
            $query->whereHas('feeStructure.batch', function ($q) use ($filters) {
                $q->where('id', $filters['batch_id']);
            });
        }
        
        return $query->orderBy('due_date')->get();
    }
    
    public function updateStudentFeeStatus(StudentFee $studentFee, $status)
    {
        try {
            DB::beginTransaction();
            
            $studentFee->update([
                'status' => $status,
            ]);
            
            DB::commit();
            
            return $studentFee;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}