<?php

// app/Modules/StudentManagement/Livewire/StudentDashboard.php
namespace App\Modules\StudentManagement\Livewire;

use Livewire\Component;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\StudentFee;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class StudentDashboard extends Component
{
    public $student;
    public $enrollments;
    public $upcomingFees;
    public $recentTransactions;
    public $totalDue = 0;
    public $showPaymentModal = false;
    public $selectedFees = [];
    public $paymentAmount = 0;
    public $paymentMethod = 'cash';
    
    public function mount()
    {
        $user = Auth::user();
        $this->student = $user->student;
        
        $this->loadData();
    }
    
    public function loadData()
    {
        $this->enrollments = $this->student->enrollments()->with('batch.programme', 'section')->get();
        
        $this->upcomingFees = StudentFee::where('student_id', $this->student->id)
            ->whereIn('status', ['pending', 'partial'])
            ->orderBy('due_date')
            ->take(5)
            ->get();
        
        $this->recentTransactions = Transaction::where('student_id', $this->student->id)
            ->orderBy('transaction_date', 'desc')
            ->take(5)
            ->get();
        
        // Calculate total due amount
        $allDueFees = StudentFee::where('student_id', $this->student->id)
            ->whereIn('status', ['pending', 'partial'])
            ->get();
        
        $this->totalDue = 0;
        foreach ($allDueFees as $fee) {
            if ($fee->status === 'pending') {
                $this->totalDue += $fee->amount;
            } else {
                $paidAmount = $fee->payments->sum('amount');
                $this->totalDue += ($fee->amount - $paidAmount);
            }
        }
    }
    
    public function openPaymentModal()
    {
        $this->selectedFees = [];
        $this->paymentAmount = 0;
        $this->showPaymentModal = true;
    }
    
    public function toggleFeeSelection($feeId)
    {
        if (in_array($feeId, $this->selectedFees)) {
            $this->selectedFees = array_diff($this->selectedFees, [$feeId]);
        } else {
            $this->selectedFees[] = $feeId;
        }
        
        $this->calculatePaymentAmount();
    }
    
    public function calculatePaymentAmount()
    {
        $this->paymentAmount = 0;
        
        foreach ($this->selectedFees as $feeId) {
            $fee = StudentFee::find($feeId);
            
            if ($fee->status === 'pending') {
                $this->paymentAmount += $fee->amount;
            } else {
                $paidAmount = $fee->payments->sum('amount');
                $this->paymentAmount += ($fee->amount - $paidAmount);
            }
        }
    }
    
    public function processPayment()
    {
        if (empty($this->selectedFees) || $this->paymentAmount <= 0) {
            session()->flash('error', 'Please select at least one fee to pay.');
            return;
        }
        
        try {
            // Create transaction record
            $transaction = Transaction::create([
                'student_id' => $this->student->id,
                'type' => 'payment',
                'amount' => $this->paymentAmount,
                'payment_method' => $this->paymentMethod,
                'status' => 'completed',
                'transaction_date' => now(),
                'reference_id' => 'PAY-' . uniqid(),
            ]);
            
            // Create payment records for each student fee
            foreach ($this->selectedFees as $feeId) {
                $studentFee = StudentFee::find($feeId);
                
                // Calculate payment amount for this fee
                $feeAmount = $studentFee->amount;
                $paidAmount = min($feeAmount, $this->paymentAmount);
                
                \App\Models\Payment::create([
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
                $this->paymentAmount -= $paidAmount;
                
                if ($this->paymentAmount <= 0) {
                    break;
                }
            }
            
            $this->showPaymentModal = false;
            $this->loadData();
            
            session()->flash('success', 'Payment processed successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error processing payment: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        // return view('studentmanagement::livewire.student-dashboard');
        return view('modules.studentmanagement.livewire.student-dashboard');
    }
}