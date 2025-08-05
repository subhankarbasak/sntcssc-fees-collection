<?php

// app/Services/PaymentService.php
namespace App\Services;

use App\Models\Transaction;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\StudentFee;
use Illuminate\Support\Facades\DB;
use App\Events\PaymentProcessed;
use App\Events\RefundProcessed;

class PaymentService
{
    public function processPayment(array $paymentData)
    {
        try {
            DB::beginTransaction();
            
            // Create transaction record
            $transaction = Transaction::create([
                'student_id' => $paymentData['student_id'],
                'type' => 'payment',
                'amount' => $paymentData['amount'],
                'payment_method' => $paymentData['payment_method'],
                'status' => 'completed',
                'transaction_date' => now(),
                'reference_id' => $paymentData['reference_id'] ?? null,
                'notes' => $paymentData['notes'] ?? null,
            ]);
            
            // Create payment records for each student fee
            foreach ($paymentData['fee_ids'] as $feeId) {
                $studentFee = StudentFee::find($feeId);
                
                // Calculate payment amount for this fee
                $feeAmount = $studentFee->amount;
                $paidAmount = min($feeAmount, $paymentData['amount']);
                
                Payment::create([
                    'transaction_id' => $transaction->id,
                    'student_fee_id' => $feeId,
                    'amount' => $paidAmount,
                    'payment_date' => now(),
                ]);
                
                // Update student fee status
                if ($paidAmount >= $feeAmount) {
                    $studentFee->status = 'paid';
                } else {
                    $studentFee->status = 'partial';
                }
                $studentFee->save();
                
                // Reduce remaining payment amount
                $paymentData['amount'] -= $paidAmount;
                
                if ($paymentData['amount'] <= 0) {
                    break;
                }
            }
            
            DB::commit();
            
            PaymentProcessed::dispatch($transaction);
            
            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function processRefund(array $refundData)
    {
        try {
            DB::beginTransaction();
            
            // Get the original transaction
            $originalTransaction = Transaction::findOrFail($refundData['transaction_id']);
            
            // Create transaction record for refund
            $transaction = Transaction::create([
                'student_id' => $originalTransaction->student_id,
                'type' => 'refund',
                'amount' => $refundData['amount'],
                'payment_method' => $refundData['payment_method'] ?? $originalTransaction->payment_method,
                'status' => 'completed',
                'transaction_date' => now(),
                'reference_id' => $refundData['reference_id'] ?? null,
                'notes' => $refundData['notes'] ?? null,
            ]);
            
            // Create refund record
            Refund::create([
                'transaction_id' => $transaction->id,
                'original_transaction_id' => $originalTransaction->id,
                'amount' => $refundData['amount'],
                'reason' => $refundData['reason'],
                'status' => 'processed',
            ]);
            
            DB::commit();
            
            RefundProcessed::dispatch($transaction);
            
            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function getStudentTransactions($studentId, $filters = [])
    {
        $query = Transaction::where('student_id', $studentId);
        
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (!empty($filters['date_from'])) {
            $query->whereDate('transaction_date', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('transaction_date', '<=', $filters['date_to']);
        }
        
        return $query->orderBy('transaction_date', 'desc')->get();
    }
    
    public function getTransactionStats($filters = [])
    {
        $query = Transaction::query();
        
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
        
        $totalPayments = $query->where('type', 'payment')->where('status', 'completed')->sum('amount');
        $totalRefunds = $query->where('type', 'refund')->where('status', 'completed')->sum('amount');
        
        return [
            'total_payments' => $totalPayments,
            'total_refunds' => $totalRefunds,
            'net_amount' => $totalPayments - $totalRefunds,
        ];
    }
}