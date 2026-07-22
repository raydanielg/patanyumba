<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    protected $fillable = ['support_chat_id', 'sender_type', 'message', 'is_read'];

    protected $casts = ['is_read' => 'boolean'];

    public function chat()
    {
        return $this->belongsTo(SupportChat::class, 'support_chat_id');
    }
}
