<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationMessage extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'status',
        'admin_seen',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
