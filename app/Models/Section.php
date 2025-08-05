<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'batch_id', 'name', 'description',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}