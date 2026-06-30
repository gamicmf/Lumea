<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    protected $table = 'user_notification';

    public $timestamps = false;
    
    protected $fillable= [
        'id', 'notification_type'
    ];

    public function notification(){
        return $this->belongsTo(Notification::class, 'id');
    }
   
}