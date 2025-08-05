<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Programme extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'description', 'duration_months',
    ];

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }
}