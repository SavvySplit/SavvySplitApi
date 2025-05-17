<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'title',
        'description',
        'amount',
        'due_date',
        'status',
        'split_with',
        'custom_splits',
        'paid',
        'paid_by',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'split_with' => 'array',
        'custom_splits' => 'array',
        'paid_by' => 'array',
        'paid' => 'boolean',
        'amount' => 'decimal:2',
    ];

    // 🔗 A bill belongs to a group
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    
}