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
        'user_id',
    ];

    // relationships
    public function contactable()
    {
        return $this->morphTo();
    }
}
