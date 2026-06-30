<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model{
    use HasFactory;

    protected $fillable = [
        'id_report',
        'id_user',
        'reportable_id',
        'reportable_type',
        'description',
        'created_at',
        'updated_at'
    ];

    public $timestamps = false;

    public function reporter(){
        return $this->belongsTo(User::class, 'id_user');
    }

    public function reportable()
    {
        if ($this->reportable_type === 'user') {
            return $this->belongsTo(User::class, 'reportable_id');
        } elseif ($this->reportable_type === 'publication') {
            return $this->belongsTo(Publication::class, 'reportable_id');
        }

        return null;
    }
}