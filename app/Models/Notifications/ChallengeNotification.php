<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengeNotification extends Model
{
    use HasFactory;

    protected $table = 'challenge_notification';

    protected $fillable= [
        'id', 'id_challenge', 'notification_type'
    ];

    public $timestamps = false;

    public function notification(){
        return $this->belongsTo(notification::class, 'id');
    }

    public function challenge(){
        return $this->belongsTo(Challenge::class, 'id_challenge');
    }
   
}
