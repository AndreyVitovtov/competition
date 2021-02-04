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
        'language'
    ];

    public function chats() {
        return $this->hasMany(Chat::class, 'users_id');
    }

}
