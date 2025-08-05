<?php

// app/Modules/FinancialManagement/Livewire/TransactionManagement.php
namespace App\Modules\FinancialManagement\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transaction;
use App\Models\Student;
use App\Models\AuditLog;

class TransactionManagement extends Component
{
    use WithPagination;
    
    public $search = '';
    public $filterType = '';
    public $filterStatus = '';
    public $filterPaymentMethod = '';
    public $filterStudent = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $showCreateModal = false;
    public $showViewModal = false;
    public $selectedTransaction = null;
    public $selectedTransactions = [];
    public $selectAll = false;
    public $bulkAction = '';
    
    // Form fields
    public $student_id = '';
    public $type = 'payment';
    public $amount = '';
    public $payment_method = 'cash';
    public $reference_id = '';
    public $notes = '';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterPaymentMethod' => ['except' => ''],
        'filterStudent' => ['except' => ''],
        'filterDateFrom' => ['except' => ''],
        'filterDateTo' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];
    
    protected $rules = [
        'student_id' => 'required|exists:students,id',
        'type' => 'required|in:payment,deposit,refund',
        'amount' => 'required|numeric|min:0',
        'payment_method' => 'required|in:cash,credit_card,debit_card,bank_transfer,upi',
        'reference_id' => 'nullable|string|max:255',
        'notes' => 'nullable|string|max:1000',
    ];
    
    public function render()
    {
        $query = Transaction::with('student.user');
        
        // Apply search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('reference_id', 'like', '%' . $this->search . '%')
                  ->orWhere('notes', 'like', '%' . $this->search . '%')
                  ->orWhereHas('student.user', function ($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }
        
        // Apply filters
        if (!empty($this->filterType)) {
            $query->where('type', $this->filterType);
        }
        
        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }
        
        if (!empty($this->filterPaymentMethod)) {
            $query->where('payment_method', $this->filterPaymentMethod);
        }
        
        if (!empty($this->filterStudent)) {
            $query->whereHas('student', function ($q) {
                $q->where('student_id', $this->filterStudent);
            });
        }
        
        if (!empty($this->filterDateFrom)) {
            $query->whereDate('transaction_date', '>=', $this->filterDateFrom);
        }
        
        if (!empty($this->filterDateTo)) {
            $query->whereDate('transaction_date', '<=', $this->filterDateTo);
        }
        
        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);
        
        $transactions = $query->paginate(10);
        $students = Student::with('user')->get();
        
        return view('financialmanagement::livewire.transaction-management', [
            'transactions' => $transactions,
            'students' => $students,
        ]);
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingFilterType()
    {
        $this->resetPage();
    }
    
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
    
    public function updatingFilterPaymentMethod()
    {
        $this->resetPage();
    }
    
    public function updatingFilterStudent()
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
            'search', 'filterType', 'filterStatus', 'filterPaymentMethod', 
            'filterStudent', 'filterDateFrom', 'filterDateTo', 'sortBy', 'sortDirection'
        ]);
    }
    
    public function openCreateModal()
    {
        $this->reset(['student_id', 'type', 'amount', 'payment_method', 'reference_id', 'notes']);
        $this->showCreateModal = true;
    }
    
    public function createTransaction()
    {
        $this->validate();
        
        try {
            $transaction = Transaction::create([
                'student_id' => $this->student_id,
                'type' => $this->type,
                'amount' => $this->amount,
                'payment_method' => $this->payment_method,
                'status' => 'completed',
                'transaction_date' => now(),
                'reference_id' => $this->reference_id,
                'notes' => $this->notes,
            ]);
            
            // Log the action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'create',
                'model_type' => Transaction::class,
                'model_id' => $transaction->id,
                'description' => "Created transaction: {$transaction->type} of ₹{$transaction->amount} for student {$transaction->student->user->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $this->showCreateModal = false;
            
            flash()->success('Transaction created successfully!');
        } catch (\Exception $e) {
            flash()->error('Error creating transaction: ' . $e->getMessage());
        }
    }
    
    public function openViewModal($transactionId)
    {
        $this->selectedTransaction = Transaction::with('student.user')->find($transactionId);
        $this->showViewModal = true;
    }
    
    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedTransactions = $this->transactions->pluck('id')->toArray();
        } else {
            $this->selectedTransactions = [];
        }
    }
    
    public function exportTransactions()
    {
        try {
            $transactions = Transaction::whereIn('id', $this->selectedTransactions)
                ->with('student.user')
                ->get();
            
            // Log the export action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'export',
                'model_type' => Transaction::class,
                'model_id' => null,
                'description' => "Exported " . count($transactions) . " transactions",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            return response()->streamDownload(function () use ($transactions) {
                $csv = fopen('php://output', 'w');
                
                // Header
                fputcsv($csv, ['ID', 'Student', 'Type', 'Amount', 'Payment Method', 'Status', 'Reference ID', 'Transaction Date']);
                
                // Data
                foreach ($transactions as $transaction) {
                    fputcsv($csv, [
                        $transaction->id,
                        $transaction->student->user->name,
                        $transaction->type,
                        $transaction->amount,
                        $transaction->payment_method,
                        $transaction->status,
                        $transaction->reference_id,
                        $transaction->transaction_date->format('Y-m-d H:i:s'),
                    ]);
                }
                
                fclose($csv);
            }, 'transactions_export_' . date('Y_m_d_His') . '.csv');
        } catch (\Exception $e) {
            flash()->error('Error exporting transactions: ' . $e->getMessage());
        }
    }
}