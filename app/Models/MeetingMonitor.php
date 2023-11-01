<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingMonitor extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'meeting_date',
        'time',
        'meeting_via',
        'location',
        'phone',
        'description',
        'contact_name',
        'company_id',
        'company_branch',
        'opportunity_id',
        'seller_id',
        'client_monitor_id',
        'contact_id',
        'participants',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'participants' => 'array',
    ];

    //relationships
    public function company() :BelongsTo
    {
        return $this->belongsTo(Company::class);
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

    public function contact() :BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
