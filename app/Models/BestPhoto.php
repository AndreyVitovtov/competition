<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BestPhoto extends Model {
    protected $table = 'best_photos';
    public $timestamps = false;
    public $fillable = [
        'id',
        'description',
        'languages_id'
    ];

    public function language() {
        return $this->belongsTo(Language::class, 'languages_id');
    }

    public function posts() {
        return $this->hasMany(PostPhoto::class, 'best_photos_id');
    }
}
