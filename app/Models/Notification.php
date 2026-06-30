<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Notifications\UserNotification;
use App\Models\Notifications\CommentNotification;
use App\Models\Notifications\GroupNotification;
use App\Models\Notifications\ChallengeNotification;
use App\Models\Notifications\PublicationNotification;
use App\Models\Notifications\AdminNotification;
use Carbon\Carbon;


class Notification extends Model
{
    use HasFactory;

    protected $table = 'notification';

    public $timestamps = false;

    protected $fillable = [
        'date', 'receive_user', 'emitter_user', 'viewed'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];
    
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receive_user');
    }

    public function emitter()
    {
        return $this->belongsTo(User::class, 'emitter_user');
    }

    public function commentNotification()
    {
        return $this->hasOne(CommentNotification::class, 'id');
    }

    public function userNotification()
    {
        return $this->hasOne(UserNotification::class, 'id','id');
    }

    public function groupNotification()
    {
        return $this->hasOne(GroupNotification::class, 'id');
    }

    public function publicationNotification()
    {
        return $this->hasOne(PublicationNotification::class, 'id');
    }

    public function challengeNotification()
    {
        return $this->hasOne(ChallengeNotification::class, 'id');
    }

    public function adminNotification()
    {
        return $this->hasOne(AdminNotification::class, 'id');
    }
}