<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channels extends Model {
    protected $table = 'channels';
    public $timestamps = false;
    public $fillable = [
        'id',
        'name',
        'channel_id',
        'languages_id'
    ];

    public function language() {
        return $this->belongsTo(Language::class, 'languages_id');
    }
}
