<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'student_id', 'dob', 'category',
    ];

    protected $casts = [
        'category' => 'string',
        'dob' => 'date',
    ];

public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function fees()
    {
        return $this->hasMany(StudentFee::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}