<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Service;
use App\Models\Location;
use App\Models\Order;
use App\Models\CustomerVehicle;
use App\Models\ServiceRequest;

class ServiceRequest extends Model
{
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'service_requests';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    protected $_status = ['0' => 'On the way','1' => 'Cancelled', '2' => 'Ready to pickup your car','3' => 'At Workshop', '4' => 'Working on it', '5' => 'On the way back' , '6' => 'All done'];
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function getCustomerName() {
        return User::where('id',$this->customer_id)->pluck('name')[0];
    }

    public function getVendorName() {
        return User::where('id',$this->vendor_id)->pluck('name')[0];
    }

    public function getServiceName() {
        return Service::where('id',$this->service_id)->pluck('service_title')[0];
    }

    public function getVehicleName() {
        return CustomerVehicle::where('id',$this->vehicle_id)->pluck('label')[0];
    }

    public function getLocationName() {
        return Location::where('id',$this->location_id)->pluck('label')[0];
    }

    public function getStatus() {
        return $this->_status[ServiceRequest::where('status',$this->status)->pluck('status')[0]];
    }

    public function getOrderID(){
        return Order::where('service_request_id',$this->id)->pluck('id')[0];
    }

    public function getOrderAmount(){
        $amount = Order::where('service_request_id',$this->id)->pluck('order_amount')[0];
        return number_format( $amount, 3 ). ' BHD';
    }
    
    public function getOrderStatus(){
        return Order::where('service_request_id',$this->id)->pluck('status')[0];
    }

   
    

    // public function getOrderID(){

    // }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function orders(){
        return $this->hasMany('App\Models\Order','service_request_id');
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
