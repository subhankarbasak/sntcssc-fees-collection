<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student2 extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_number',
        'admission_test_roll_no',
        'programme_name',
        'batch',
        'student_id',
        'section',
        'first_name',
        'last_name',
        'district',
        'address',
        'category',
        'dob',
        'gender',
        'email',
        'alternate_email',
        'mobile',
        'alternate_mobile',
        'whatsapp',
        'is_pwbd',
        'occupation',
        'father_name',
        'mother_name',
        'father_occupation',
        'mother_occupation',
        'family_income',
        'selection_type',
        'score_A',
        'score_B',
        'score_C',
        'score_D',
        'status',
        'note',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'dob' => 'date',
        'is_pwbd' => 'boolean',
        'family_income' => 'decimal:2',
        'score_A' => 'decimal:2',
        'score_B' => 'decimal:2',
        'score_C' => 'decimal:2',
        'score_D' => 'decimal:2',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}