<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikeComment extends Model
{
    use HasFactory;

    protected $table = 'like_comments';

    protected $fillable = [
        'id_post',
        'id_user',
        'id_comment',
    ];

    public $timestamps = false;
}
