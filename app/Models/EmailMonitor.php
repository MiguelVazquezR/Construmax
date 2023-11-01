<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class EmailMonitor extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'subject',
        'content',
        'contact_name',
        'contact_email',
        'branch',
        'seller_id',
        'opportunity_id',
        'customer_id',
        'client_monitor_id',
        'contact_id',
    ];

    //relationships
    public function customer() :BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function opportunity() :BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function seller() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contact() :MorphOne
    {
        return $this->morphOne(Contact::class, 'contactable');
    }

    public function clientMonitor() :BelongsTo
    {
        return $this->belongsTo(ClientMonitor::class);
    }
}
