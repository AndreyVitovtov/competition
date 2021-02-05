<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostPhoto extends Model {
    protected $table = 'posts_photos';
    public $timestamps = false;
    public $fillable = [
        'id',
        'channel_id',
        'post_id',
        'count_likes',
        'users_id',
        'best_photos_id'
    ];

    public function bestPhoto() {
        return $this->belongsTo(BestPhoto::class, 'best_photos_id');
    }

    public function user() {
        return $this->belongsTo(BotUsers::class, 'users_id');
    }
}
