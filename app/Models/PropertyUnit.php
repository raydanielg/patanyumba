<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyUnit extends Model
{
    protected $fillable = [
        'property_id', 'unit_name', 'unit_number', 'price',
        'bedrooms', 'bathrooms', 'area_sqm', 'floor_number',
        'max_occupants', 'is_furnished', 'is_available',
        'amenities', 'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_furnished' => 'boolean',
        'is_available' => 'boolean',
        'amenities' => 'array',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
