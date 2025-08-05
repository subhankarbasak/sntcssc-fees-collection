<?php

// app/Services/ReportingService.php
namespace App\Services;

use App\Models\Student;
use App\Models\Transaction;
use App\Models\StudentFee;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class ReportingService
{
    public function getStudentCountReport($filters = [])
    {
        $query = Student::query();
        
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
        
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        
        return $query->count();
    }
    
    public function getFeeCollectionReport($filters = [])
    {
        $query = Transaction::where('type', 'payment')->where('status', 'completed');
        
        if (!empty($filters['date_from'])) {
            $query->whereDate('transaction_date', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('transaction_date', '<=', $filters['date_to']);
        }
        
        if (!empty($filters['batch_id'])) {
            $query->whereHas('student.enrollments', function ($q) use ($filters) {
                $q->where('batch_id', $filters['batch_id']);
            });
        }
        
        if (!empty($filters['programme_id'])) {
            $query->whereHas('student.enrollments.batch.programme', function ($q) use ($filters) {
                $q->where('id', $filters['programme_id']);
            });
        }
        
        $totalAmount = $query->sum('amount');
        
        return [
            'total_amount' => $totalAmount,
            'transaction_count' => $query->count(),
            'average_amount' => $query->count() > 0 ? $totalAmount / $query->count() : 0,
        ];
    }
    
    public function getOutstandingFeesReport($filters = [])
    {
        $query = StudentFee::whereIn('status', ['pending', 'partial']);
        
        if (!empty($filters['batch_id'])) {
            $query->whereHas('feeStructure.batch', function ($q) use ($filters) {
                $q->where('id', $filters['batch_id']);
            });
        }
        
        if (!empty($filters['programme_id'])) {
            $query->whereHas('feeStructure.batch.programme', function ($q) use ($filters) {
                $q->where('id', $filters['programme_id']);
            });
        }
        
        if (!empty($filters['due_from'])) {
            $query->whereDate('due_date', '>=', $filters['due_from']);
        }
        
        if (!empty($filters['due_to'])) {
            $query->whereDate('due_date', '<=', $filters['due_to']);
        }
        
        $totalAmount = $query->sum('amount');
        
        // Calculate paid amount for partial fees
        $paidAmount = 0;
        foreach ($query->get() as $fee) {
            if ($fee->status === 'partial') {
                $paidAmount += $fee->payments->sum('amount');
            }
        }
        
        return [
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'outstanding_amount' => $totalAmount - $paidAmount,
            'fee_count' => $query->count(),
        ];
    }
    
    public function getRefundReport($filters = [])
    {
        $query = Transaction::where('type', 'refund')->where('status', 'completed');
        
        if (!empty($filters['date_from'])) {
            $query->whereDate('transaction_date', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('transaction_date', '<=', $filters['date_to']);
        }
        
        $totalAmount = $query->sum('amount');
        
        return [
            'total_amount' => $totalAmount,
            'transaction_count' => $query->count(),
            'average_amount' => $query->count() > 0 ? $totalAmount / $query->count() : 0,
        ];
    }
    
    public function getEnrollmentReport($filters = [])
    {
        $query = Enrollment::query();
        
        if (!empty($filters['batch_id'])) {
            $query->where('batch_id', $filters['batch_id']);
        }
        
        if (!empty($filters['programme_id'])) {
            $query->whereHas('batch.programme', function ($q) use ($filters) {
                $q->where('id', $filters['programme_id']);
            });
        }
        
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (!empty($filters['date_from'])) {
            $query->whereDate('enrollment_date', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('enrollment_date', '<=', $filters['date_to']);
        }
        
        $enrollments = $query->get();
        
        return [
            'total_enrollments' => $enrollments->count(),
            'by_status' => $enrollments->groupBy('status')->map->count(),
            'by_programme' => $enrollments->groupBy(function ($enrollment) {
                return $enrollment->batch->programme->name;
            })->map->count(),
        ];
    }
    
    public function getFinancialSummary($filters = [])
    {
        $feeCollection = $this->getFeeCollectionReport($filters);
        $outstandingFees = $this->getOutstandingFeesReport($filters);
        $refunds = $this->getRefundReport($filters);
        
        return [
            'fee_collection' => $feeCollection,
            'outstanding_fees' => $outstandingFees,
            'refunds' => $refunds,
            'net_collection' => $feeCollection['total_amount'] - $refunds['total_amount'],
        ];
    }
}