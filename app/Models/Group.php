<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $table = 'group_users';

    protected $fillable = [
        'name',
        'creation_date',
        'description',
        'max_participants',
        'public'
    ];

    public $timestamps = false;

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_member', 'id_group', 'id_user');
    }

    public function owner()
    {
        return $this->hasOne(User::class, 'id', 'id_owner');
    }
    public function messages()
    {
        return $this->hasMany(Message::class, 'id_group');
    }
}
