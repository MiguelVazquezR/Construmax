<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'contactable_type',
        'contactable_id',
        'name',
        'email',
        'phone',
        'position',
        'additional',
        'user_id',
    ];

    protected $casts = [
        'additional' => 'array',
    ];

    // relationships
    public function contactable()
    {
        return $this->morphTo();
    }
}
