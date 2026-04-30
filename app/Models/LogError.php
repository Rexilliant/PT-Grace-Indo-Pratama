<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogError extends Model
{
    protected $table = 'log_errors';

    protected $fillable = [
        'message',
        'file',
        'line',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
