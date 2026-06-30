<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    use HasFactory;

    protected $table = 'admin_notification';

    protected $fillable= [
        'id',
        'id_admin',
        'id_report',
        'notification_type',
    ];

    public $timestamps = false;
    
    public function notification(){
        return $this->belongsTo(notification::class, 'id');
    }

    public function admin(){
        return $this->belongsTo(Admin::class, 'id_user');
    }
   
}
