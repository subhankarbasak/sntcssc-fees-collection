<?php

// app/Modules/Reporting/Livewire/Reports.php
namespace App\Modules\Reporting\Livewire;

use Livewire\Component;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\Enrollment;
use App\Models\StudentFee;
use App\Models\Programme;
use App\Models\Batch;
use App\Models\FeeStructure;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentReportExport;
use App\Exports\TransactionReportExport;
use App\Exports\FeeReportExport;
use App\Exports\EnrollmentReportExport;

class Reports extends Component
{
    public $reportType = 'students';
    public $filterProgramme = '';
    public $filterBatch = '';
    public $filterStatus = '';
    public $filterCategory = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $reportData = [];
    public $showPreview = false;
    
    public function generateReport()
    {
        $this->reportData = [];
        
        switch ($this->reportType) {
            case 'students':
                $this->generateStudentReport();
                break;
            case 'transactions':
                $this->generateTransactionReport();
                break;
            case 'fees':
                $this->generateFeeReport();
                break;
            case 'enrollments':
                $this->generateEnrollmentReport();
                break;
        }
        
        $this->showPreview = true;
    }
    
    private function generateStudentReport()
    {
        $query = Student::with('user', 'enrollments.batch.programme');
        
        if (!empty($this->filterProgramme)) {
            $query->whereHas('enrollments.batch.programme', function ($q) {
                $q->where('id', $this->filterProgramme);
            });
        }
        
        if (!empty($this->filterBatch)) {
            $query->whereHas('enrollments', function ($q) {
                $q->where('batch_id', $this->filterBatch);
            });
        }
        
        if (!empty($this->filterCategory)) {
            $query->where('category', $this->filterCategory);
        }
        
        if (!empty($this->filterDateFrom)) {
            $query->whereDate('created_at', '>=', $this->filterDateFrom);
        }
        
        if (!empty($this->filterDateTo)) {
            $query->whereDate('created_at', '<=', $this->filterDateTo);
        }
        
        $this->reportData = $query->get();
    }
    
    private function generateTransactionReport()
    {
        $query = Transaction::with('student.user');
        
        if (!empty($this->filterDateFrom)) {
            $query->whereDate('transaction_date', '>=', $this->filterDateFrom);
        }
        
        if (!empty($this->filterDateTo)) {
            $query->whereDate('transaction_date', '<=', $this->filterDateTo);
        }
        
        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }
        
        $this->reportData = $query->get();
    }
    
    private function generateFeeReport()
    {
        $query = StudentFee::with('student.user', 'feeStructure.feeType', 'feeStructure.batch.programme');
        
        if (!empty($this->filterProgramme)) {
            $query->whereHas('feeStructure.batch.programme', function ($q) {
                $q->where('id', $this->filterProgramme);
            });
        }
        
        if (!empty($this->filterBatch)) {
            $query->whereHas('feeStructure.batch', function ($q) {
                $q->where('id', $this->filterBatch);
            });
        }
        
        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }
        
        if (!empty($this->filterCategory)) {
            $query->whereHas('feeStructure', function ($q) {
                $q->where('category', $this->filterCategory);
            });
        }
        
        if (!empty($this->filterDateFrom)) {
            $query->whereDate('due_date', '>=', $this->filterDateFrom);
        }
        
        if (!empty($this->filterDateTo)) {
            $query->whereDate('due_date', '<=', $this->filterDateTo);
        }
        
        $this->reportData = $query->get();
    }
    
    private function generateEnrollmentReport()
    {
        $query = Enrollment::with('student.user', 'batch.programme');
        
        if (!empty($this->filterProgramme)) {
            $query->whereHas('batch.programme', function ($q) {
                $q->where('id', $this->filterProgramme);
            });
        }
        
        if (!empty($this->filterBatch)) {
            $query->where('batch_id', $this->filterBatch);
        }
        
        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }
        
        if (!empty($this->filterDateFrom)) {
            $query->whereDate('enrollment_date', '>=', $this->filterDateFrom);
        }
        
        if (!empty($this->filterDateTo)) {
            $query->whereDate('enrollment_date', '<=', $this->filterDateTo);
        }
        
        $this->reportData = $query->get();
    }
    
    public function exportReport()
    {
        try {
            switch ($this->reportType) {
                case 'students':
                    return Excel::download(new StudentReportExport($this->reportData), 'students_report_' . date('Y_m_d_His') . '.xlsx');
                case 'transactions':
                    return Excel::download(new TransactionReportExport($this->reportData), 'transactions_report_' . date('Y_m_d_His') . '.xlsx');
                case 'fees':
                    return Excel::download(new FeeReportExport($this->reportData), 'fees_report_' . date('Y_m_d_His') . '.xlsx');
                case 'enrollments':
                    return Excel::download(new EnrollmentReportExport($this->reportData), 'enrollments_report_' . date('Y_m_d_His') . '.xlsx');
                default:
                    flash()->error('Invalid report type.');
                    return null;
            }
        } catch (\Exception $e) {
            flash()->error('Error exporting report: ' . $e->getMessage());
            return null;
        }
    }
    
    public function render()
    {
        $programmes = Programme::all();
        $batches = Batch::with('programme')->get();
        
        return view('reporting::livewire.reports', [
            'programmes' => $programmes,
            'batches' => $batches,
        ]);
    }
}