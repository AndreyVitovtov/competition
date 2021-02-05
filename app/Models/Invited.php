<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invited extends Model {
    protected $table = 'invited';
    public $timestamps = false;
    public $fillable = [
        'add_to_group_id',
        'referrer',
        'referral'
    ];

    public function addToGroup() {
        return $this->belongsTo(AddToGroup::class, 'add_to_group_id');
    }

    public function referrer() {
        return $this->belongsTo(BotUsers::class, 'referrer');
    }

    public function referral() {
        return $this->belongsTo(BotUsers::class, 'referral');
    }
}
