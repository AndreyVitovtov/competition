<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikesPhoto extends Model {
    protected $table = 'likes_photos';
    public $timestamps = false;
    public $fillable = [
        'posts_photos_id',
        'user_chat'
    ];

    public function post() {
        return $this->belongsTo(PostPhoto::class, 'posts_photos_id');
    }

    public function user() {
        return $this->belongsTo(BotUsers::class, 'users_id');
    }
}
