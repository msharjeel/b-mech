<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerVehicle extends Model
{
    use CrudTrait;
    use HasFactory;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'vehicles';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    protected $dates = ['deleted_at'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function getVehicleMake()
    {
        return VehicleMake::where('id',$this->vehicle_make)->pluck('name')[0];
    }

    public function getVehicleModel()
    {
        return VehicleModel::where('id',$this->vehicle_model)->pluck('name')[0];
    }

    public function getCustomerName(){

        return User::where('id',$this->user_id)->pluck('name')[0];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function models(){
        return $this->hasMany('App\Models\VehicleModel');
    }

    public function makes(){
        return $this->hasMany('App\Models\VehicleMake');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
