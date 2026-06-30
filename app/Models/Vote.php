<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    // Nome correto da tabela
    protected $table = 'vote';

    protected $fillable = [
        'id_post',
        'id_publication',
        'aesthetic',
        'technique',
        'creativity',
        'rate',
        'created_date',
    ];

    public $timestamps = false;

    public function publication()
    {
        return $this->belongsTo(Publication::class, 'id_publication');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'id_post');
    }
}
