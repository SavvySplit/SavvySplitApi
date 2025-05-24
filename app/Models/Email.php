<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'subject',
        'from',
        'body',
        'attachments',
        'ocr_text',
        'ai_result',
    ];

    protected $casts = [
        'attachments' => 'array', // Automatically cast attachments as array
    ];

   
}
