<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Comment;

class CommentImage extends Model
{
    protected $fillable = [
        'comment_id',
        'image_path'
    ];
    
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}