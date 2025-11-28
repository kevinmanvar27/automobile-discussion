<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Thread;

class ThreadImage extends Model
{
    protected $fillable = [
        'thread_id',
        'image_path'
    ];
    
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }
}