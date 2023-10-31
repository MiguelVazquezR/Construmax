<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'currency',
        'rfc',
        'user_id',
        'invoicing_method',
        'payment_method',
        'invoice_use',
    ];

    // relationships
    public function contacts()
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class);
    }

    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
