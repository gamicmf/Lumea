<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;
    protected $table = 'challenge'; 

    protected $fillable = [
        'id',
        'id_creator',
        'name',
        'description',
        'begin_date',
        'end_date',
        'max_participants',
        'private',
    ];
    public $timestamps = false;

    public function participants()
    {
        return $this->belongsToMany(User::class, 'challenge_participants', 'id_challenge', 'id_user');
    }
}
