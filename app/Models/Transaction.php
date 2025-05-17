<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'payer_id',
        'amount',
        'date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'datetime',
    ];

    // 🔗 A transaction belongs to a bill
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    // 🔗 A transaction is made by a user (payer)
    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');
    }
}
