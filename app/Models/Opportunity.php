<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Opportunity extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'priority',
        'probability',
        'service_type',
        'status',
        'description',
        'lost_oportunity_razon',
        'amount',
        'start_date',
        'close_date',
        'finished_at',
        'paid_at',
        'contact_id',
        'customer_id',
        'customer_name',
        'contact_name',
        'contact_phone',
        'branch',
        'user_id',
        'seller_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'close_date' => 'date',
        'finished_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // relationships
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function opportunityTasks() :HasMany
    {
        return $this->hasMany(OpportunityTask::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
        ->withPivot([
            'id',
            'permissions',
        ])->withTimestamps();
    }
}
