<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentNotification extends Model
{
    use HasFactory;

    protected $table = 'comment_notification';

    public $timestamps = false;

    protected $fillable= [
        'id','id_comment', 'notification_type'
    ];

    public function notification(){
        return $this->belongsTo(Notification::class, 'id');
    }

    public function comment(){
        return $this->belongsTo(Comment::class, 'id_comment');
    }
   
}