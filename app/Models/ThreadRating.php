<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Thread;
use App\Models\User;

class ThreadRating extends Model
{
    protected $fillable = [
        'thread_id',
        'user_id',
        'rating'
    ];

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}