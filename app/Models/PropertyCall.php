<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyCall extends Model
{
    protected $fillable = [
        'property_id', 'caller_id', 'receiver_id',
        'call_type', 'status', 'duration_seconds', 'contact_phone',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function caller()
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
