<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicationNotification extends Model
{
    use HasFactory;

    protected $table = 'publication_notification';
    
    public $timestamps = false;

    protected $fillable= [
        'id', 'id_publication', 'notification_type'
    ];

    public function notification(){
        return $this->belongsTo(Notification::class, 'id');
    }

    public function publication(){
        return $this->belongsTo(Publication::class, 'id_publication');
    }
   
}