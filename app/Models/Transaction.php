<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'student_id', 'type', 'amount', 'payment_method', 'status', 'transaction_date', 'reference_id', 'notes',
    ];

    protected $casts = [
        'type' => 'string',
        'amount' => 'decimal:2',
        'payment_method' => 'string',
        'status' => 'string',
        'transaction_date' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function originalRefunds()
    {
        return $this->hasMany(Refund::class, 'original_transaction_id');
    }
}