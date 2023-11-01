<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'seller_id',
        'opportunity_id',
        'company_id',
        'client_monitor_id',
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
}
