<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ContactsType extends Model {
    protected $table = 'contacts_type';
    public $timestamps = false;
    public $fillable = [
        'id',
        'type'
    ];

    public function contacts() {
        return $this->hasMany(ContactsModel::class, 'contacts_type_id');
    }
}
