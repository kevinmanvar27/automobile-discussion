<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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
    
    // Property to hold the user rating
    private $userRatingValue = null;

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
        // If userRatingValue is set, return it
        if ($this->userRatingValue !== null) {
            return $this->userRatingValue;
        }
        
        // Otherwise, try to get it from the ratings relationship
        if (Auth::check() && $this->relationLoaded('ratings')) {
            $userRating = $this->ratings->where('user_id', Auth::id())->first();
            return $userRating ? $userRating->rating : 0;
        }
        
        // Default to 0
        return 0;
    }
    
    // Method to set the user rating value
    public function setUserRating($rating)
    {
        $this->userRatingValue = $rating;
    }
}