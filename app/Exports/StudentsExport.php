<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;
    protected $search;
    protected $sortField;
    protected $sortDirection;
    protected $showDeleted;
    protected $selectedIds;

    public function __construct($filters, $search, $sortField, $sortDirection, $showDeleted, $selectedIds = [])
    {
        $this->filters = $filters;
        $this->search = $search;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
        $this->showDeleted = $showDeleted;
        $this->selectedIds = $selectedIds;
    }

    public function query()
    {
        $query = $this->showDeleted ? Student::withTrashed() : Student::query();

        if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        } else {
            $query->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search . '%')
                      ->orWhere('student_id', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filters['category'], fn ($query) => $query->where('category', $this->filters['category']))
            ->when($this->filters['status'], fn ($query) => $query->where('status', $this->filters['status']))
            ->when($this->filters['programme_name'], fn ($query) => $query->where('programme_name', 'like', '%' . $this->filters['programme_name'] . '%'))
            ->when($this->filters['batch'], fn ($query) => $query->where('batch', $this->filters['batch']));
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    public function headings(): array
    {
        return [
            'Student ID',
            'Application Number',
            'First Name',
            'Last Name',
            'Programme Name',
            'Batch',
            'Section',
            'Email',
            'Category',
            'Status',
            'Score A',
            'Score B',
            'Score C',
            'Score D',
            'Deleted At',
        ];
    }

    public function map($student): array
    {
        return [
            $student->student_id,
            $student->application_number,
            $student->first_name,
            $student->last_name,
            $student->programme_name,
            $student->batch,
            $student->section,
            $student->email,
            $student->category,
            $student->status,
            $student->score_A,
            $student->score_B,
            $student->score_C,
            $student->score_D,
            $student->deleted_at ? $student->deleted_at->toDateTimeString() : '',
        ];
    }
}