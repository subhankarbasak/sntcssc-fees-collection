<?php

// app/Modules/Dashboard/Livewire/AdminDashboard.php
namespace App\Modules\Dashboard\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\Enrollment;
use App\Models\FeeStructure;
use App\Models\StudentFee;
use App\Models\AuditLog;

class AdminDashboard extends Component
{
    public $totalUsers;
    public $totalStudents;
    public $totalEnrollments;
    public $totalTransactions;
    public $totalPayments;
    public $totalRefunds;
    public $recentTransactions;
    public $recentUsers;
    public $recentStudents;
    public $feeCollectionStats;
    public $outstandingFeesStats;
    public $enrollmentStats;
    public $dateRange = 'this_month';
    public $chartData;
    public $auditLogs;
    
    public function mount()
    {
        $this->loadData();
    }
    
    public function updatedDateRange()
    {
        $this->loadData();
    }
    
    public function loadData()
    {
        $dateFilters = $this->getDateFilters();
        
        $this->totalUsers = User::count();
        $this->totalStudents = Student::count();
        $this->totalEnrollments = Enrollment::count();
        $this->totalTransactions = Transaction::count();
        
        $this->totalPayments = Transaction::where('type', 'payment')
                                          ->where('status', 'completed')
                                          ->when($dateFilters, function ($query, $dates) {
                                              return $query->whereBetween('transaction_date', [$dates['start'], $dates['end']]);
                                          })
                                          ->sum('amount');
        
        $this->totalRefunds = Transaction::where('type', 'refund')
                                         ->where('status', 'completed')
                                         ->when($dateFilters, function ($query, $dates) {
                                             return $query->whereBetween('transaction_date', [$dates['start'], $dates['end']]);
                                         })
                                         ->sum('amount');
        
        $this->recentTransactions = Transaction::with('student.user')
                                               ->orderBy('transaction_date', 'desc')
                                               ->take(5)
                                               ->get();
        
        $this->recentUsers = User::orderBy('created_at', 'desc')
                                   ->take(5)
                                   ->get();
        
        $this->recentStudents = Student::with('user')
                                       ->orderBy('created_at', 'desc')
                                       ->take(5)
                                       ->get();
        
        $this->feeCollectionStats = $this->getFeeCollectionReport($dateFilters);
        $this->outstandingFeesStats = $this->getOutstandingFeesReport();
        $this->enrollmentStats = $this->getEnrollmentReport($dateFilters);
        
        $this->auditLogs = AuditLog::with('user')
                                   ->orderBy('created_at', 'desc')
                                   ->take(10)
                                   ->get();
        
        $this->loadChartData();
    }
    
    private function getDateFilters()
    {
        $now = now();
        
        switch ($this->dateRange) {
            case 'today':
                return [
                    'start' => $now->startOfDay(),
                    'end' => $now->endOfDay(),
                ];
            case 'this_week':
                return [
                    'start' => $now->startOfWeek(),
                    'end' => $now->endOfWeek(),
                ];
            case 'this_month':
                return [
                    'start' => $now->startOfMonth(),
                    'end' => $now->endOfMonth(),
                ];
            case 'this_year':
                return [
                    'start' => $now->startOfYear(),
                    'end' => $now->endOfYear(),
                ];
            default:
                return null;
        }
    }
    
    private function getFeeCollectionReport($dateFilters)
    {
        $query = Transaction::where('type', 'payment')
                              ->where('status', 'completed');
        
        if ($dateFilters) {
            $query->whereBetween('transaction_date', [$dateFilters['start'], $dateFilters['end']]);
        }
        
        return [
            'total' => $query->sum('amount'),
            'count' => $query->count(),
        ];
    }
    
    private function getOutstandingFeesReport()
    {
        $pendingFees = StudentFee::where('status', 'pending')->sum('amount');
        $partialFees = StudentFee::where('status', 'partial')->get();
        
        $partialAmount = 0;
        foreach ($partialFees as $fee) {
            $paidAmount = $fee->payments->sum('amount');
            $partialAmount += ($fee->amount - $paidAmount);
        }
        
        return [
            'pending' => $pendingFees,
            'partial' => $partialAmount,
            'total' => $pendingFees + $partialAmount,
        ];
    }
    
    private function getEnrollmentReport($dateFilters)
    {
        $query = Enrollment::query();
        
        if ($dateFilters) {
            $query->whereBetween('enrollment_date', [$dateFilters['start'], $dateFilters['end']]);
        }
        
        return [
            'total' => $query->count(),
            'active' => $query->where('status', 'active')->count(),
            'completed' => $query->where('status', 'completed')->count(),
            'inactive' => $query->where('status', 'inactive')->count(),
            'dropped' => $query->where('status', 'dropped')->count(),
        ];
    }
    
    private function loadChartData()
    {
        $this->chartData = [
            'labels' => [],
            'payments' => [],
            'refunds' => [],
        ];
        
        if ($this->dateRange === 'this_month') {
            $daysInMonth = now()->daysInMonth;
            
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = now()->setDay($day)->format('Y-m-d');
                $this->chartData['labels'][] = $day;
                
                $payments = Transaction::where('type', 'payment')
                                      ->where('status', 'completed')
                                      ->whereDate('transaction_date', $date)
                                      ->sum('amount');
                
                $refunds = Transaction::where('type', 'refund')
                                     ->where('status', 'completed')
                                     ->whereDate('transaction_date', $date)
                                     ->sum('amount');
                
                $this->chartData['payments'][] = $payments;
                $this->chartData['refunds'][] = $refunds;
            }
        } elseif ($this->dateRange === 'this_year') {
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            foreach ($months as $index => $month) {
                $this->chartData['labels'][] = $month;
                
                $monthStart = now()->month($index + 1)->startOfMonth();
                $monthEnd = now()->month($index + 1)->endOfMonth();
                
                $payments = Transaction::where('type', 'payment')
                                      ->where('status', 'completed')
                                      ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                                      ->sum('amount');
                
                $refunds = Transaction::where('type', 'refund')
                                     ->where('status', 'completed')
                                     ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                                     ->sum('amount');
                
                $this->chartData['payments'][] = $payments;
                $this->chartData['refunds'][] = $refunds;
            }
        }
    }
    
    public function render()
    {
        // return view('dashboard::livewire.admin-dashboard');
        return view('modules.dashboard.livewire.admin-dashboard');
    }
}