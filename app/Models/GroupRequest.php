<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupRequest extends Model
{
    use HasFactory;

    protected $table = 'group_join_request';

    protected $fillable = [
        'id_user',
        'id_group'
    ];

    public $timestamps = false;


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'id_group');
    }
}
