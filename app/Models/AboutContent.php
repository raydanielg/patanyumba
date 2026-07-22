<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutContent extends Model
{
    protected $fillable = ['section', 'title', 'content', 'icon', 'image_url', 'stats', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'stats' => 'array',
    ];
}
