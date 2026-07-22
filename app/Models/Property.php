<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'property_type', 'price', 'currency',
        'contact_phone', 'listing_type', 'total_units',
        'rental_period', 'price_min', 'price_max',
        'region', 'district', 'ward', 'street', 'exact_location',
        'latitude', 'longitude', 'bedrooms', 'bathrooms', 'area_sqm',
        'is_furnished', 'is_available', 'status', 'is_featured', 'is_sponsored',
        'featured_until', 'sponsored_until', 'views_count', 'unlock_count', 'amenities',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_min' => 'decimal:2',
        'price_max' => 'decimal:2',
        'is_furnished' => 'boolean',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'is_sponsored' => 'boolean',
        'featured_until' => 'datetime',
        'sponsored_until' => 'datetime',
        'amenities' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
