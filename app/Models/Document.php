<?php

// app/Models/Document.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'type',
        'file_path',
        'file_name',
        'size',
        'extension',
        'tags',
        'mime_type',
        'expires_at',
        'is_public',
        'description',
        'source',
        'uploaded_at',
        'verified_at',
        'verification_status',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tags' => 'array',
        'expires_at' => 'date',
        'uploaded_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_public' => 'boolean',
        'status' => 'boolean',
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}