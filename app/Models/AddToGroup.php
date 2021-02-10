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
        'languages_id',
        'active',
        'date',
        'time'
    ];

    public function language() {
        return $this->belongsTo(Language::class, 'languages_id');
    }

    public function groups() {
        return $this->hasMany(Groups::class, 'add_to_group_id');
    }
}
