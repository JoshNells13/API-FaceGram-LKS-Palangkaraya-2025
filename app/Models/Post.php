<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','caption'];
    protected $hidden = ['deleted_at'];

    public $timestamp = false;


    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function Attachment(){
        return $this->hasMany(Attachment::class);
    }
}
