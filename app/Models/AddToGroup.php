<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddToGroup extends Model {
    protected $table = 'add_to_group';
    public $timestamps = false;
    public $fillable = [
        'id',
        'description',
        'group_id',
        'group_link',
        'languages_id',
        'date',
        'time'
    ];

    public function language() {
        return $this->belongsTo(Language::class, 'languages_id');
    }

    public function invited() {
        return $this->hasMany(Invited::class, 'add_to_group_id');
    }
}
