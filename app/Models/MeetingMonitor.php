<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class MeetingMonitor extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'meeting_date',
        'time',
        'meeting_via',
        'location',
        'contact_phone',
        'contact_name',
        'description',
        'branch',
        'participants',
        'customer_id',
        'opportunity_id',
        'seller_id',
        'client_monitor_id',
        'contact_id',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'participants' => 'array',
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

    public function clientMonitor() :BelongsTo
    {
        return $this->belongsTo(ClientMonitor::class);
    }

    public function contact()
    {
        return $this->morphOne(Contact::class, 'contactable');
    }
}
