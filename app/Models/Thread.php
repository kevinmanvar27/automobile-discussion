<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Comment;
use App\Models\ThreadImage;
use App\Models\ThreadRating;

class Thread extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'subject',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    public function images()
    {
        return $this->hasMany(ThreadImage::class);
    }
    
    public function ratings()
    {
        return $this->hasMany(ThreadRating::class);
    }
    
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }
    
    public function getUserRatingAttribute()
    {
        // This will be set dynamically when needed
        return 0;
    }
}