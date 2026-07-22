<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['name', 'code', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function districts()
    {
        return $this->hasMany(District::class)->orderBy('sort_order')->orderBy('name');
    }
}
