<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BestVideo extends Model {
    protected $table = 'best_videos';
    public $timestamps = false;
    public $fillable = [
        'id',
        'languages_id',
        'description'
    ];

    public function language() {
        return $this->belongsTo(Language::class, 'languages_id');
    }

    public function posts() {
        return $this->hasMany(PostVideo::class, 'best_videos_id');
    }
}
