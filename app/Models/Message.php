<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_group',
        'id_user',
        'content',
    ];

    public $timestamps = false;

    protected $dates = ['created_at'];

    public function group()
    {
        return $this->belongsTo(Group::class, 'id_group');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value);
    }
}