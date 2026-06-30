<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user'
    ];
    protected $table = 'administrator'; // Specify the table name
    public $timestamps = false; // If the table doesn't have created_at/updated_at columns

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
