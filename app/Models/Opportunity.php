<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'priority',
        'status',
        'amount',
        'close_date',
        'contact_id',
        'user_id',
        'seller_id',
    ];

    protected $casts = [
        'close_date' => 'date',
    ];

    // relationships
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'id', 'seller_id');
    }
}
