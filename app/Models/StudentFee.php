<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
    protected $fillable = [
        'student_id', 'fee_structure_id', 'due_date', 'amount', 'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string',
        'due_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}