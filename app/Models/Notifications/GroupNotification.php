<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Notification;

class GroupNotification extends Model
{
    use HasFactory;

    protected $table = 'group_notification';

    protected $fillable= [
        'id', 'id_group', 'notification_type'
    ];

    public $timestamps = false;

    public function notification(){
        return $this->belongsTo(Notification::class, 'id');
    }

    public function group(){
        return $this->belongsTo(Group::class, 'id_group');
    }
   
}