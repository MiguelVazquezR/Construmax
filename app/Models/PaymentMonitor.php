<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PaymentMonitor extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'paid_at',
        'amount',
        'payment_method',
        'concept',
        'notes',
        'seller_id',
        'opportunity_id',
        'client_monitor_id',
    ];

    protected $casts = [
        'paid_at' => 'datetime'
    ];

    //relationships
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
