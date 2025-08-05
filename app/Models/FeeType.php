<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    protected $fillable = [
        'name', 'description', 'frequency', 'is_refundable',
    ];

    protected $casts = [
        'frequency' => 'string',
        'is_refundable' => 'boolean',
    ];

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }
}