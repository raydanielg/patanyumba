<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportChat extends Model
{
    protected $fillable = ['user_id', 'subject', 'status', 'last_message_at'];

    protected $casts = ['last_message_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(SupportMessage::class);
    }

    public function unreadCount()
    {
        return $this->messages()->where('sender_type', 'user')->where('is_read', false)->count();
    }
}
