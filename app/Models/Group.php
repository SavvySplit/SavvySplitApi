<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        //'members',
        'description',
        'color',
    ];

    protected $casts = [
        'members' => 'array',
        'color' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔗 Relationship: One group has many bills
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    // 🔗 Get user models for the group members
    public function memberUsers()
    {
        return User::whereIn('id', $this->members)->get();
    }
}
