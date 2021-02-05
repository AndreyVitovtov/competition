<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model {
    protected $table = 'languages';
    public $timestamps = false;
    public $fillable = [
        'id',
        'name',
        'code',
        'emoji'
    ];

    public function bestVideos() {
        return $this->hasMany(BestVideo::class, 'languages_id');
    }

    public function bestPhotos() {
        return $this->hasMany(BestPhoto::class, 'languages_id');
    }

    public function channel() {
        return $this->hasOne(Channels::class, 'languages_id');
    }
}
