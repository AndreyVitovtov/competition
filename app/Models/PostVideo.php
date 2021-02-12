<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostVideo extends Model {
    protected $table = 'post_videos';
    public $timestamps = false;
    public $fillable = [
        'id',
        'post_id',
        'count_likes',
        'users_id',
        'best_videos_id'
    ];

    public function user() {
        return $this->belongsTo(BotUsers::class, 'users_id');
    }

    public function bestVideo() {
        return $this->belongsTo(BestVideo::class, 'best_videos_id');
    }

    public function likes() {
        return $this->hasMany(
            LikesVideo::class,
            'posts_videos_id'
        );
    }
}
