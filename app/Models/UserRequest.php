<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Notifications\Notifiable;

class UserRequest extends Model
{
    use HasFactory, Notifiable, CrudTrait;

    protected $table = 'user_request';
}
