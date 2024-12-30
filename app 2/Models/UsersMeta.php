<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Notifications\Notifiable;

class UsersMeta extends Model
{
    use HasFactory, Notifiable, CrudTrait;

    protected $table = 'users_meta';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    //protected $fillable = ['user_id'];
    // protected $hidden = [];
    // protected $dates = [];
    
    // protected $casts = [
    //     'amount' => 'array',
    // ];


   
}
