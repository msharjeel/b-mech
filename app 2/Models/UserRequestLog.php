<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Notifications\Notifiable;

use App\Models\User;
use App\Models\Service;
use App\Models\Location;
use App\Models\Order;
use App\Models\CustomerVehicle;
use App\Models\ServiceRequest;

class UserRequestLog extends Model
{
    use HasFactory, Notifiable, CrudTrait;

    protected $table = 'user_request_log';


    public function getCustomerName() {
        return User::where('id',$this->user_id)->pluck('name')[0];
    }

    public function getVendorName() {
        return User::where('id',$this->vendor_id)->pluck('name')[0];
    }

    public function getServiceName() {
        return Service::where('id',$this->service_id)->pluck('service_title')[0];
    }

    public function getAcceptedName() {
        if( $this->rejected_by !=NULL ){
            return 'Rejected';
        }else{
            return 'Timeout';
        }
        
    }

  

}
