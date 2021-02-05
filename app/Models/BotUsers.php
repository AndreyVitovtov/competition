<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class BotUsers extends Model {
    protected $table = "users";
    public $timestamps = false;
    public $fillable = [
        'id',
        'chat',
        'username',
        'first_name',
        'last_name',
        'country',
        'messenger',
        'access',
        'date',
        'time',
        'active',
        'start',
        'count_ref',
        'access_free',
        'languages_id'
    ];

    public function videos() {
        return $this->hasMany(PostVideo::class, 'users_id');
    }

    public function photos() {
        return $this->hasMany(PostPhoto::class, 'users_id');
    }

    public function language() {
        return $this->belongsTo(Language::class, 'languages_id');
    }
}
