<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'commentaires';

    protected $fillable = [
        'id_post',
        'id_publication',
        'comment_text',
        'created_date',
        'previous',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'id_post');
    }
        public function post()
    {
        return $this->belongsTo(Post::class, 'id_post');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'previous');
    }

    public static function getReplies($parentId)
    {
        return self::where('previous', $parentId)->orderBy('created_date', 'asc')->get();
    }

    public function likes()
    {
        return $this->hasMany(LikeComment::class, 'id_comment');
    }



   
}
