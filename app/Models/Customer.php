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
        'branches',
        'currency',
        'rfc',
        'zipcode',
        'user_id',
        'contact_name',
        'contact_phone',
        'contact_email',
        'invoicing_method',
        'payment_method',
        'invoice_use',
    ];

    protected $casts = [
        'branches' => 'array',
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

    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
