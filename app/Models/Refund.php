<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = [
        'transaction_id', 'original_transaction_id', 'amount', 'reason', 'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function originalTransaction()
    {
        return $this->belongsTo(Transaction::class, 'original_transaction_id');
    }
}