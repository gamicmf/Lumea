<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowRequest extends Model
{
    use HasFactory;

    protected $table = 'follow_request';

    protected $fillable = [
        'id_follower',
        'id_followed'
    ];

    public $timestamps = false;


    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_follower');
    }

    public function followed(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_followed');
    }
}
