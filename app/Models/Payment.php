<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'tx_id', 'user_id', 'property_id', 'subscription_id',
        'payment_type', 'amount', 'currency', 'method',
        'provider_tx_id', 'status', 'failure_reason', 'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
