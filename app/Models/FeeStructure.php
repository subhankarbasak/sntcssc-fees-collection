<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    protected $fillable = [
        'programme_id', 'batch_id', 'fee_type_id', 'amount', 'category',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'category' => 'string',
    ];

    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }

    public function studentFees()
    {
        return $this->hasMany(StudentFee::class);
    }
}