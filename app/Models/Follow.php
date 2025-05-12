<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;

    protected $fillable = ['following_id','follower_id','is_accepted'];

    public function following(){
        return $this->belongsTo(User::class, 'following_id');
    }

    public function follower(){
        return $this->belongsTo(User::class,'follower_id');
    }   

    public $timestamps = false;

}
