<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClientMonitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'date',
        'concept',
        'monitor_id', //no es una llave foranea.
        'seller_id',
        'opportunity_id',
        'customer_id',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    //relationships

    public function seller() :BelongsTo 
    {
        return $this->belongsTo(User::class);
    }

    public function opportunity() :BelongsTo 
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function customer() :BelongsTo 
    {
        return $this->belongsTo(Customer::class);
    }

    public function emailMonitor() :HasOne 
    {
        return $this->hasOne(EmailMonitor::class);
    }

    public function paymentMonitor() :HasOne 
    {
        return $this->hasOne(PaymentMonitor::class);
    }

    public function meetingMonitor() :HasOne 
    {
        return $this->hasOne(MeetingMonitor::class);
    }

}
