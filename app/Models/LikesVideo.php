<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikesVideo extends Model {
    protected $table = 'likes_videos';
    public $timestamps = false;
    public $fillable = [
        'post_videos_id',
        'user_chat'
    ];

    public function post() {
        return $this->belongsTo(PostVideo::class, 'posts_videos_id');
    }

    public function user() {
        return $this->belongsTo(BotUsers::class, 'users_id');
    }

}
