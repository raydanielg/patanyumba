<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyImage extends Model
{
    protected $fillable = ['property_id', 'media_type', 'image_path', 'video_url', 'thumbnail_path', 'thumbnail_url', 'is_primary', 'sort_order'];

    protected $casts = ['is_primary' => 'boolean'];

    protected $appends = ['url', 'image_url', 'video_link', 'thumbnail_link'];

    public function getUrlAttribute()
    {
        if ($this->media_type === 'video') {
            return $this->video_url;
        }
        $path = $this->image_path;
        if ($path && str_starts_with($path, 'http')) {
            return $path;
        }
        return $path ? url($path) : null;
    }

    public function getImageUrlAttribute()
    {
        if ($this->media_type === 'video') {
            return $this->thumbnail_url ? ($this->thumbnail_url) : null;
        }
        return $this->url;
    }

    public function getVideoLinkAttribute()
    {
        $path = $this->video_url;
        if ($path && str_starts_with($path, 'http')) {
            return $path;
        }
        return $path ? url($path) : null;
    }

    public function getThumbnailLinkAttribute()
    {
        $path = $this->thumbnail_url ?? $this->thumbnail_path;
        if ($path && str_starts_with($path, 'http')) {
            return $path;
        }
        return $path ? url($path) : null;
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
