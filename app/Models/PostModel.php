<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Laravel\Scout\Searchable;

class PostModel extends Model
{
    use Searchable;
    use HasFactory;
    protected $table = 'posts';

    protected $fillable = ['title', 'body', 'user_id'];

    public function toSearchableArray(){
        return [
            'title'=> $this->title,
            'body' => $this->body

        ];
    }

    //blogpost belongs to the user. We have only defined the relationship from one direction
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
