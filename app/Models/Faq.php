<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = ['id_user', 'question', 'answer'];

    protected $table = 'faq';

    public $timestamps = false;

    protected $dates = ['created_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
