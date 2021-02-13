<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Others extends Model {
    protected $table = 'others';
    public $timestamps = false;
    public $fillable = [
        'id',
        'description',
        'date',
        'time',
        'languages_id'
    ];
}
