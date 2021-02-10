<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invited extends Model {
    protected $table = 'invited';
    public $timestamps = false;
    public $fillable = [
        'groups_id',
        'referrer',
        'referral'
    ];

    public function group() {
        return $this->belongsTo(AddToGroup::class, 'add_to_group_id');
    }
}
