<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// Added to define Eloquent relationships.
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;
    
    public function isAdmin()
    {
        return Admin::where('id_user', $this->id)->exists();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'username', 'email', 'password', 'birthdate', 'description', 'public', 'points', 'ranking', 'profile_picture','blocked', 'google_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function notification()
    {
        return $this->hasMany(Notification::class, 'received_user');
    }

    public function publications(){
        return $this->hasManyThrough(
            Publication::class,   
            Post::class,          
            'id_poster',          
            'id_post',            
            'id',                 
            'id'                  
        );
    }
    //função para mostrar os users que seguem o user    
    public function followers(){
        return $this->belongsToMany(User::class,
         'followers',//nome da tabela
         'user_id',// foreign key para o user seguido 
         'follower_id' //foreign key para o user que segue
        );
    }

    //função para mostrar os users que o user segue
    public function following(){
        return $this->belongsToMany(User::class,
            'followers',//nome da tabela
            'follower_id',// foreign key para o user que segue
            'user_id' //foreign key para o user seguido
        );
    }
    //função para verificar se o user segue outro user
    public function isFollowing(User $user){
        return $this->following()->where('users.id', $user->id)->exists();
    }

    public function countFollowers(){
        return $this->followers()->count();
    }

    public function countFollowing(){
        return $this->following()->count();
    }
    
    public function isBlocked()
    {
        return $this->blocked; // Assuming you have a 'blocked' column in your users table
    }
}
