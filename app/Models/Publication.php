<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Post
{
    use HasFactory;
    protected $table = 'publications';

    protected $fillable = [
        'id_post', 'id_challenge', 'pub_image', 'ranking', 'created_date', 'description'
    ];

    public $timestamps = false;

    public function post(){
        return $this->belongsTo(Post::class, 'id_post');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'id_post');
    }

}