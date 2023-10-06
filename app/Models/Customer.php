<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'branches',
        'currency',
        'rfc',
        'zipcode',
        'user_id',
    ];

    // relationships
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function tags()
    {
        return $this->morphMany(Tag::class, 'taggable');
    }

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class);
    }
}
