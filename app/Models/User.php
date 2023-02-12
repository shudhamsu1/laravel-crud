<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
    ];
        //using accessor -the attribute is the accessor, filter the value before the metaphor for the value gets populated
    protected function avatar():Attribute{
        return Attribute::make(get:function($value){
            //$value is the incoming data from the database
            //if the avatar field is empty its should be fallback image
            //if the value is true it goes to storage/avatars/value of the database else we will use the fallback avatar image
            return $value ? '/storage/avatars/'.$value :  '/fallback-avatar.jpg';

        });
    }
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
    ];

    public function posts(){
        return $this->hasMany(PostModel::class, 'user_id');
    }
}
