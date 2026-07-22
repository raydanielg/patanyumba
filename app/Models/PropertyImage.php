<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyImage extends Model
{
    protected $fillable = ['property_id', 'image_path', 'thumbnail_path', 'is_primary', 'sort_order'];

    protected $casts = ['is_primary' => 'boolean'];

    protected $appends = ['url', 'image_url'];

    public function getUrlAttribute()
    {
        $path = $this->image_path;
        if ($path && str_starts_with($path, 'http')) {
            return $path;
        }
        return $path ? url($path) : null;
    }

    public function getImageUrlAttribute()
    {
        return $this->url;
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
