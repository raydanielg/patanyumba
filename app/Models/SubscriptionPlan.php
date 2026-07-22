<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price', 'currency',
        'billing_cycle', 'target_audience', 'unlock_limit', 'is_active', 'features',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'features' => 'array',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
