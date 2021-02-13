<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groups extends Model {
    protected $table = 'groups';
    public $timestamps = false;
    public $fillable = [
        'id',
        'group_id',
        'group_link',
        'add_to_group_id'
    ];

    public function invited() {
        return $this->hasMany(Invited::class, 'groups_id');
    }

    public function addToGroup() {
        return $this->belongsTo(AddToGroup::class, 'add_to_group_id');
    }
}
