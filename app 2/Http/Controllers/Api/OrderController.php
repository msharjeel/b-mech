<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\Location;
use App\Models\CustomerVehicle;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\VehicleTransmission;
use App\Models\VehicleCylinder;
use App\Models\VehicleDrive;
use App\Models\VehicleClass;
use App\Models\VehicleDisplacement;
use App\Models\UsersMeta;
use App\Models\ServiceCategory;
use App\Models\UserRequest;
use Carbon\Carbon;
use App\Models\Review;
use App\Models\Coupon;

class OrderController extends Controller
{
    //
    public function listOrders( Request $request  , $id ){

        $_userId        =   $id;
        $_status        =   $request->filter;
        $_vendorList    = [];
        if( $_status =='' ){
          //  echo 'lll';
            $_order      =  Order::select('orders.id', 'services.service_title','orders.created_at','orders.updated_at','orders.status','orders.status as order_status','service_requests.status as service_status', 'services.icon_image as _image', 'orders.order_amount as _amount',
                                            'service_requests.vendor_id as _vendor','service_requests.vehicle_id as vehicle_id', 'service_requests.location_id as location_id' , 'service_requests.service_id as service_id' , 'orders.paid_status as paid_status', 'orders.payment_through as payment_through','services.type as service_type',  'services.service_duration as service_duration','service_requests.updated_at as serv_updated_at'
                                            ,'users.name as user_name','coupons.name as coupon_name','coupons.amount as coupon_amount' ,'coupons.type as coupon_type','orders.order_rated as rated')
                                ->distinct()
                                ->rightJoin('services','orders.service_id','=','services.id')
                                ->rightJoin('users','orders.user_id','=','users.id')
                                ->rightJoin('service_requests','orders.service_request_id','=','service_requests.id')
                                //->rightJoin('coupons','orders.coupon_id','=','coupons.id')
                                ->leftJoin('coupons',function($join){
                                    $join
                                    ->on('orders.coupon_id','=','coupons.id');
                                    //->orWhereNull('orders.coupon_id');
                                  })
                                ->where('user_id','=',$_userId)
                                ->orderBy('created_at', 'desc')
                                ->get()
                                ->toArray();
        }else{
            if(  $_status =='All' ){
             //  echo 'sss';
                $_order     =  Order::select('orders.id', 'services.service_title','orders.created_at','orders.updated_at','orders.status','orders.status as order_status','service_requests.status as service_status', 'services.icon_image as _image', 'orders.order_amount as _amount',
                                                'service_requests.vendor_id as _vendor','service_requests.vehicle_id as vehicle_id', 'service_requests.location_id as location_id', 'service_requests.service_id as service_id' ,'orders.paid_status as paid_status', 'orders.payment_through as payment_through','services.type as service_type' ,  'services.service_duration as service_duration',
                                                'service_requests.updated_at as serv_updated_at' ,'users.name as user_name','coupons.name as coupon_name','coupons.amount as coupon_amount','coupons.type as coupon_type','orders.order_rated as rated')
                                    ->distinct()
                                    ->rightJoin('services','orders.service_id','=','services.id')
                                    ->rightJoin('users','orders.user_id','=','users.id')
                                    ->rightJoin('service_requests','orders.service_request_id','=','service_requests.id')
                                    //->rightJoin('coupons','orders.coupon_id','=','coupons.id')
                                    ->leftJoin('coupons',function($join){
                                        $join
                                        ->on('orders.coupon_id','=','coupons.id');
                                        //->orWhereNull('orders.coupon_id');
                                      })
                                    ->where('orders.user_id','=',$_userId)
                                    ->whereIn('orders.status',['processing','complete','cancel'])
                                    //->where('orders.status','=',$_status)
                                    ->orderBy('created_at', 'desc')
                                    ->get()
                                    ->toArray();
            }elseif( $_status =='Active' ){
                //echo  $_userId.'AAA';
                $_order     =  Order::select('orders.id', 'services.service_title','orders.created_at','orders.updated_at','orders.status','orders.status as order_status','service_requests.status as service_status', 'services.icon_image as _image', 'orders.order_amount as _amount',
                                             'service_requests.vendor_id as _vendor','service_requests.vehicle_id as vehicle_id', 'service_requests.location_id as location_id', 
                                             'service_requests.service_id as service_id' ,'orders.paid_status as paid_status', 'orders.payment_through as payment_through',
                                             'services.type as service_type' ,  'services.service_duration as service_duration','service_requests.updated_at as serv_updated_at',
                                             'users.name as user_name','coupons.name as coupon_name','coupons.amount as coupon_amount','coupons.type as coupon_type','orders.order_rated as rated')
                                    ->distinct()
                                    ->rightJoin('services','orders.service_id','=','services.id')
                                    ->rightJoin('users','orders.user_id','=','users.id')
                                    ->rightJoin('service_requests','orders.service_request_id','=','service_requests.id')
                                    ->leftJoin('coupons',function($join){
                                        $join
                                        ->on('orders.coupon_id','=','coupons.id');
                                        //->orWhereNull('orders.coupon_id');
                                      })
                                    ->whereIn('service_requests.status',[0,3,2,4])
                                    ->whereIn('orders.status',['processing'])
                                   // ->orWhere('service_requests.status','=', 3)
                                    //->orWhere('service_requests.status','=', 4)
                                   // ->orWhere('service_requests.status','=', 1)
                                    ->where('orders.user_id','=',$_userId)
                                    ->where('service_requests.customer_id','=',$_userId)
                                    ->orderBy('created_at', 'desc')
                                    ->get()
                                    ->toArray();
            }
            else{
              
                $_order     =  Order::select('orders.id', 'services.service_title','orders.created_at','orders.updated_at','orders.status','orders.status as order_status','service_requests.status as service_status', 'services.icon_image as _image', 'orders.order_amount as _amount',
                                                'service_requests.vendor_id as _vendor','service_requests.vehicle_id as vehicle_id', 'service_requests.location_id as location_id' , 'service_requests.service_id as service_id' ,
                                                'orders.paid_status as paid_status', 'orders.payment_through as payment_through','services.type as service_type',  'services.service_duration as service_duration',
                                                'service_requests.updated_at as serv_updated_at'  ,'users.name as user_name','coupons.name as coupon_name','coupons.amount as coupon_amount','coupons.type as coupon_type','orders.order_rated as rated')
                                    ->distinct()
                                    ->rightJoin('services','orders.service_id','=','services.id')
                                    ->rightJoin('users','orders.user_id','=','users.id')
                                    //->rightJoin('coupons','orders.coupon_id','=','coupons.id')
                                    ->leftJoin('coupons',function($join){
                                        $join
                                        ->on('orders.coupon_id','=','coupons.id');
                                        //->orWhereNull('orders.coupon_id');
                                      })
                                    ->rightJoin('service_requests','orders.service_request_id','=','service_requests.id')
                                    ->where('orders.user_id','=',$_userId)
                                    ->where('service_requests.status','=',$_status)
                                    ->orderBy('created_at', 'desc')
                                    ->get()
                                    ->toArray();
            }
            
        }

        $_activeOrder     =  Order::select('orders.id', 'services.service_title','orders.created_at','orders.updated_at','orders.status','orders.status as order_status','service_requests.status as service_status', 'services.icon_image as _image', 'orders.order_amount as _amount',
                                                'service_requests.vendor_id as _vendor','service_requests.vehicle_id as vehicle_id', 'service_requests.location_id as location_id' , 'service_requests.service_id as service_id',
                                                 'orders.user_id as _userID' ,  'services.service_duration as service_duration','service_requests.updated_at as serv_updated_at','users.name as user_name',
                                                 'coupons.name as coupon_name','coupons.amount as coupon_amount','coupons.type as coupon_type','orders.order_rated as rated')
                                ->distinct()
                                ->rightJoin('services','orders.service_id','=','services.id')
                                ->rightJoin('users','orders.user_id','=','users.id')
                                //->rightJoin('coupons','orders.coupon_id','=','coupons.id')
                                ->leftJoin('coupons',function($join){
                                    $join
                                    ->on('orders.coupon_id','=','coupons.id');
                                    //->orWhereNull('orders.coupon_id');
                                  })
                                ->rightJoin('service_requests','orders.service_request_id','=','service_requests.id')
                                ->where('orders.user_id','=',$_userId)
                                ->where('orders.status','=','processing')
                                ->where('service_requests.status','!=',1)
                                //->where('service_requests.status','=','3')
                                ->orderBy('created_at', 'desc')
                                ->get()
                                ->toArray();
        
        $_address   =   [];
        $vehicle    =   [];
        $__activeOrder       =   [];
        if( !empty($_activeOrder) ){
            foreach( $_activeOrder as $orderK => $orderD ){

                $__activeOrder[$orderK]['_order'] = $orderD; 

                $vehicle     = CustomerVehicle::select('vehicles.vehicle_year as year','vehicle_makes.name as make','vehicle_transmissions.name as transmission','vehicle_classes.name as classes','vehicle_model.name as model',
                                                        'vehicles.label as label','vehicles.vehicle_country as _countryID', 'vehicles.vehicle_class	 as _classID',
                                                        'vehicles.km_run as km', 'vehicles.fuel_type as fuel_type','vehicles.id as _vid')
                                                        ->distinct()
                                                        ->rightJoin('vehicle_makes','vehicles.vehicle_make','=','vehicle_makes.id')
                                                        ->rightJoin('vehicle_transmissions','vehicles.vehicle_transmission','=','vehicle_transmissions.id')
                                                        ->rightJoin('vehicle_classes','vehicles.vehicle_class','=','vehicle_classes.id')
                                                        //->rightJoin('countries','vehicles.vehicle_country','=','countries.id')
                                                        //->rightJoin('vehicle_displacements','vehicles.vehicle_displacement','=','vehicle_displacements.id')
                                                        //->rightJoin('vehicle_drives','vehicles.vehicle_drive','=','vehicle_drives.id')
                                                        ->rightJoin('vehicle_model','vehicles.vehicle_model','=','vehicle_model.id')
                                                        ->where('vehicles.user_id',$orderD['_userID'])
                                                        ->where('vehicles.id', $orderD['vehicle_id'])
                                                        ->where('vehicles.active',1)
                                                        ->get()
                                                        ->toArray();
                $__activeOrder[$orderK]['_vehicle'] = $vehicle;

                $_address       =  Location::select('locations.*', 'vendor_locations.name as _area','locations.id as _lID')
                                            ->distinct()
                                            ->where('user_id', '=', $orderD['_userID'])
                                            ->rightJoin('vendor_locations','locations.area','=','vendor_locations.id')
                                            //->where('active',1)
                                            ->where('locations.id', $orderD['location_id'])
                                            ->get()->toArray(); 
                $__activeOrder[$orderK]['_location'] = $_address;

                $__vendorList        =   UsersMeta::select('users.id as _id','users.name as vendor_name','users.image as _image','users.service_duration as _duration','users.latitude as _latitude','users.longitude as _longitude')
                                                            ->distinct()
                                                            ->rightJoin('users','users_meta.user_id','=','users.id')
                                                            ->where('users.id',$orderD['_vendor'])
                                                            ->get()->toArray();
                if( !empty($__vendorList) ){
                    foreach( $__vendorList as $_kl => $_lv ){

                        $reviews        = Review::where('vendor_id',$_lv['_id'])
                                        ->sum('rating');
                        $reviewsCount   = Review::where('vendor_id',$_lv['_id'])
                                            ->count();

                        $latitudeFrom   = $_address[0]['latitude'];
                        $longitudeFrom  = $_address[0]['longitude'];
                        $latitudeTo     = $_lv['_latitude'];
                        $longitudeTo    = $_lv['_longitude'];
        
                        $theta      = $longitudeFrom - $longitudeTo;
                        $dist       = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
                        $dist       = acos($dist);
                        $dist       = rad2deg($dist);
                        $miles      = $dist * 60 * 1.1515;
                        $_km        = round($miles * 1.609344, 2).' km';
                            
                        $__activeOrder[$orderK]['_vendor'] = [
                            'id'            => $_lv['_id'],
                            'vendor_name'   => $_lv['vendor_name'],
                            '_image'        => $_lv['_image'],
                            '_duration'     => $_lv['_duration'],
                            '_reviews'      =>  ( $reviewsCount ) ? round((  $reviews  / $reviewsCount ),1) : 0,
                            '_distance'     =>  $_km,
                        ];
                    }
                }
                

            }
        }
        
        $_data    = [];
        if( !empty($_order) ){
            foreach( $_order as $_k => $_d ){
                //print_r( $_d);
                $_data[$_k]['_order'] = $_d;

                $__vehicle     = CustomerVehicle::select('vehicles.vehicle_year as year','vehicle_makes.name as make','vehicle_transmissions.name as transmission','vehicle_classes.name as classes','vehicle_model.name as model',
                                                'vehicles.label as label','vehicles.vehicle_country as _countryID', 'vehicles.vehicle_class	 as _classID',
                                                'vehicles.km_run as km', 'vehicles.fuel_type as fuel_type','vehicles.id as _vid')
                                                ->distinct()
                                                ->rightJoin('vehicle_makes','vehicles.vehicle_make','=','vehicle_makes.id')
                                                ->rightJoin('vehicle_transmissions','vehicles.vehicle_transmission','=','vehicle_transmissions.id')
                                                ->rightJoin('vehicle_classes','vehicles.vehicle_class','=','vehicle_classes.id')
                                                //->rightJoin('countries','vehicles.vehicle_country','=','countries.id')
                                                //->rightJoin('vehicle_displacements','vehicles.vehicle_displacement','=','vehicle_displacements.id')
                                                //->rightJoin('vehicle_drives','vehicles.vehicle_drive','=','vehicle_drives.id')
                                                ->rightJoin('vehicle_model','vehicles.vehicle_model','=','vehicle_model.id')
                                                ->where('vehicles.user_id',$_userId)
                                                ->where('vehicles.id', $_d['vehicle_id'])
                                               // ->where('vehicles.active',1)
                                                ->get()
                                                ->toArray();
                $_data[$_k]['_vehicle'] = $__vehicle;

                $__address       =  Location::select('locations.*', 'vendor_locations.name as _area','locations.id as _lID')
                                                ->distinct()
                                                ->where('user_id', '=', $_userId)
                                                ->rightJoin('vendor_locations','locations.area','=','vendor_locations.id')
                                                //->where('active',1)
                                                ->where('locations.id', $_d['location_id'])
                                                ->get()->toArray(); 
                $_data[$_k]['_location'] = $__address;

                $__vendorList        =   UsersMeta::select('users.id as _id','users.name as vendor_name','users.image as _image','users.service_duration as _duration','users.latitude as _latitude','users.longitude as _longitude')
                                            ->distinct()
                                            ->rightJoin('users','users_meta.user_id','=','users.id')
                                            ->where('users.id',$_d['_vendor'])
                                            ->get()->toArray();

                if( !empty($__vendorList) ){
                    foreach( $__vendorList as $_kl => $_lv ){

                        $reviews        = Review::where('vendor_id',$_lv['_id'])
                                        ->sum('rating');
                        $reviewsCount   = Review::where('vendor_id',$_lv['_id'])
                                            ->count();
                        $latitudeFrom   = $__address[0]['latitude'];
                        $longitudeFrom  = $__address[0]['longitude'];
                        $latitudeTo     = $_lv['_latitude'];
                        $longitudeTo    = $_lv['_longitude'];
        
                        $theta      = $longitudeFrom - $longitudeTo;
                        $dist       = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
                        $dist       = acos($dist);
                        $dist       = rad2deg($dist);
                        $miles      = $dist * 60 * 1.1515;
                        $_km        = round($miles * 1.609344, 2).' km';

                        $_data[$_k]['_vendor'] = [
                            'id'            => $_lv['_id'],
                            'vendor_name'   => $_lv['vendor_name'],
                            '_image'        => $_lv['_image'],
                            '_duration'     => $_lv['_duration'],
                            '_reviews'      =>  ( $reviewsCount ) ? round((  $reviews  / $reviewsCount ),1) : 0,
                            '_distance'     =>  $_km,
                        ];
                    }
                }
               
                //$_data[$_k]['_vendor'] = $__vendorList;

            }
        }                   
                                    
        
        if( !empty($_order) ){
            return response()->json([
                'status'        => true,
                'message'       => 'Success',
                'data'          => $_data,
                'active_order'  =>  ['_order'     =>  $__activeOrder],
               // '_location'     =>  $_address,
               // '_vehicle'      =>  $vehicle
            ],200);
        }else{
            return response()->json([
                'status'        => true,
                'message'       => 'No data',
                'data'          => [
             
                ],
                'active_order'  =>  [
                    '_order'     =>  [],
                    '_location'  =>  [],
                    '_vehicle'   =>  [],
                    '_vendor'    =>  []

                 ],
            ],200);
        }
    }

    public function detailedOrder( Request $request , $id ){

        $orderId            =   $id;
        $response           =   [];

        $_orderDetail       = Order::where('id','=',$orderId )
                                ->get()
                                ->toArray();

        if( !empty($_orderDetail) ){
            foreach( $_orderDetail as $_k => $d ){

                $response['service_request']                =   $this->getServiceRequest($d['service_request_id']);
                $response['order']['order_id']              =   $d['id'];
                $response['order']['order_status']          =   $d['status'];
                $response['order']['order_paid_status']     =   $d['paid_status'];
                $response['order']['order_amount']          =   $d['order_amount'];
                $response['order']['created_at']            =   $d['created_at'];
                $response['order']['updated_at']            =   $d['updated_at'];
                $response['user']                           =   $this->getUserDetails($d['user_id']);
                $response['service']                        =   $this->getServiceDetails($d['service_id']);
                $response['vendor']                         =   $this->getServiceRequest($d['service_request_id'])['vendor'];
                $response['location']                       =   $this->getServiceRequest($d['service_request_id'])['location'];
                $response['vehicle']                        =   $this->getServiceRequest($d['service_request_id'])['vehicle'];
               
            }
        }
        //print_r($response);

        if( !empty($response) ){
            return response()->json([
                'status'        => true,
                'message'       => 'Success',
                'data'          => $response,
            ],200);
        }else{
            return response()->json([
                'status'        => true,
                'message'       => 'No data',
                'data'          => $response,
            ],200);
        }
    }

    private function getServiceDetails( $servcie_id ){

         $service       =   Service::where('id',$servcie_id)
                                ->get()
                                ->toArray()[0];
         return $service;
    }

    private function getUserDetails( $user_id ){

        $user       =   User::where('id',$user_id)
                            ->get()
                            ->toArray()[0];
        return $user;
    }

    private function getServiceRequest( $request_id ){

        $status         = ['0' => 'On the way', '1' => 'Cancelled','2' => 'Ready to pickup your car', '3' => 'Working on it' , '4' => 'On the way back', '5' => 'All done'];

        $_request       =   ServiceRequest::where('id',$request_id)
                                ->select('customer_id','vendor_id','location_id','status','vehicle_id')
                                ->get()
                                ->toArray()[0];

        $_request['_status']    =   $status[$_request['status']];
        $_request['location']   =   $this->getUserLocation($_request['location_id']);
        $_request['vendor']     =   $this->getVendorDetails($_request['vendor_id']);
        $_request['vehicle']    =   $this->getVehicleDetails($_request['vehicle_id']);

        return $_request;
    }

    private function getVendorDetails( $vendor_id ){

        $vendor     =   User::where('id',$vendor_id)
                            ->get()
                            ->toArray()[0];

        return  $vendor;
    }

    private function getUserLocation( $location_id ){

        $location       = Location::where('id',$location_id)
                            ->get()->toArray()[0];

        return $location;
    }

    private function getVehicleDetails( $vehicle_id ){
        
        $vehicle     = CustomerVehicle::select('vehicles.vehicle_year as year','vehicle_makes.name as make','vehicle_transmissions.name as transmission','vehicle_classes.name as classes','vehicle_model.name as model')
                        ->distinct()
                        ->rightJoin('vehicle_makes','vehicles.vehicle_make','=','vehicle_makes.id')
                        ->rightJoin('vehicle_transmissions','vehicles.vehicle_transmission','=','vehicle_transmissions.id')
                        ->rightJoin('vehicle_classes','vehicles.vehicle_class','=','vehicle_classes.id')
                       // ->rightJoin('vehicle_cylinders','vehicles.vehicle_cylinder','=','vehicle_cylinders.id')
                       // ->rightJoin('vehicle_displacements','vehicles.vehicle_displacement','=','vehicle_displacements.id')
                       // ->rightJoin('vehicle_drives','vehicles.vehicle_drive','=','vehicle_drives.id')
                        ->rightJoin('vehicle_model','vehicles.vehicle_model','=','vehicle_model.id')
                        ->where('vehicles.id',$vehicle_id)
                        ->get()
                        ->toArray()[0];
    
        return $vehicle;
        
    }


    public function getVendorOrder( Request $request  , $id ){
        
        $_userId        =   $id;
        $_status        =   $request->filter;
        $_req           =   $request->order;

        if( $_status =='' ||  $_req =='Active' ){
           // echo 'asdasd';
            $_order      =  Order::select('orders.id', 'services.service_title','orders.created_at','orders.updated_at','orders.status','orders.status as order_status','service_requests.status as service_status', 'services.icon_image as _image', 'orders.order_amount as _amount',
                                            'service_requests.vendor_id as _vendor','service_requests.vehicle_id as vehicle_id', 'service_requests.location_id as location_id' 
                                            ,'orders.user_id as _userID','orders.service_request_id as service_request_id','orders.service_id as service_id','orders.service_cat_id as service_cat_id', 
                                            'services.type as _type' ,  'services.service_duration as service_duration','service_requests.updated_at as serv_updated_at','users.name as user_name','coupons.name as coupon_name','coupons.amount as coupon_amount','coupons.type as coupon_type')
                                ->distinct()
                                ->rightJoin('services','orders.service_id','=','services.id')
                                ->rightJoin('users','orders.user_id','=','users.id')
                                ->rightJoin('service_requests','orders.service_request_id','=','service_requests.id')
                                //->rightJoin('coupons','orders.coupon_id','=','coupons.id')
                                    ->leftJoin('coupons',function($join){
                                        $join
                                        ->on('orders.coupon_id','=','coupons.id');
                                    //->orWhereNull('orders.coupon_id');
                                    })
                                ->where('service_requests.vendor_id','=',$_userId)
                                //->where('service_requests.accepted_by','!=',null)
                                ->orderBy('created_at', 'desc')
                                ->where('orders.status','=','processing')
                                ->whereIn('service_requests.status',[0,3,4,2])
                                ->get()
                                ->toArray();

        }else{
            if( $_status =='All' ){
              //  echo $_status;
                $_order     =  Order::select('orders.id', 'services.service_title','orders.created_at','orders.updated_at','orders.status','orders.status as order_status','service_requests.status as service_status', 'services.icon_image as _image', 'orders.order_amount as _amount',
                                                'service_requests.vendor_id as _vendor','service_requests.vehicle_id as vehicle_id', 'service_requests.location_id as location_id' 
                                                ,'orders.user_id as _userID','orders.service_request_id as service_request_id','orders.service_id as service_id','orders.service_cat_id as service_cat_id', 
                                                'orders.paid_status as paid_status', 'orders.payment_through as payment_through','services.type as service_type', 'services.type as _type',  
                                                'services.service_duration as service_duration','service_requests.updated_at as serv_updated_at','users.name as user_name','coupons.name as coupon_name','coupons.amount as coupon_amount','coupons.type as coupon_type')
                                    ->distinct()
                                    ->rightJoin('services','orders.service_id','=','services.id')
                                    ->rightJoin('users','orders.user_id','=','users.id')
                                    ->rightJoin('service_requests','orders.service_request_id','=','service_requests.id')
                                    //->rightJoin('coupons','orders.coupon_id','=','coupons.id')
                                    ->leftJoin('coupons',function($join){
                                        $join
                                        ->on('orders.coupon_id','=','coupons.id');
                                        //->orWhereNull('orders.coupon_id');
                                      })
                                    ->where('service_requests.vendor_id','=',$_userId)
                                    ->whereIn('orders.status',['processing','complete','cancel'])
                                    ->orderBy('created_at', 'desc')
                                   // ->where('services.service_title', 'LIKE', '%'.$_status.'%')
                                    ->get()
                                    ->toArray();
            }elseif( $_status =='Active'){
               // echo 'adsad';
                $_order     =  Order::select('orders.id', 'services.service_title','orders.created_at','orders.updated_at','orders.status','orders.status as order_status','service_requests.status as service_status', 'services.icon_image as _image', 'orders.order_amount as _amount',
                                                'service_requests.vendor_id as _vendor','service_requests.vehicle_id as vehicle_id', 'service_requests.location_id as location_id' 
                                                ,'orders.user_id as _userID','orders.service_request_id as service_request_id','orders.service_id as service_id','orders.service_cat_id as service_cat_id',
                                                 'orders.paid_status as paid_status', 'orders.payment_through as payment_through','services.type as service_type', 'services.type as _type' , 
                                                  'services.service_duration as service_duration' ,'service_requests.updated_at as serv_updated_at' ,'users.name as user_name','coupons.name as coupon_name','coupons.amount as coupon_amount','coupons.type as coupon_type')
                                    ->distinct()
                                    ->rightJoin('services','orders.service_id','=','services.id')
                                    ->rightJoin('users','orders.user_id','=','users.id')
                                    ->rightJoin('service_requests','orders.service_request_id','=','service_requests.id')
                                    //->rightJoin('coupons','orders.coupon_id','=','coupons.id')
                                    ->leftJoin('coupons',function($join){
                                        $join
                                        ->on('orders.coupon_id','=','coupons.id');
                                        //->orWhereNull('orders.coupon_id');
                                      })
                                    ->where('service_requests.vendor_id','=',$_userId)
                                    ->where('orders.status','=','processing')
                                    ->whereIn('service_requests.status',[0,3,4,2])
                                  // ->groupBy('coupons.id')
                                   // ->where('service_requests.status','=',0)
                                    // ->orWhere('service_requests.status','=', 3)
                                    // ->orWhere('service_requests.status','=', 4)
                                    // ->orWhere('service_requests.status','=', 1)
                                    ->orderBy('created_at', 'desc')
                                    
                                    ->get()
                                    ->toArray();

            }else{
                $_order     =  Order::select('orders.id', 'services.service_title','orders.created_at','orders.updated_at','orders.status','orders.status as order_status','service_requests.status as service_status', 'services.icon_image as _image', 'orders.order_amount as _amount',
                                                'service_requests.vendor_id as _vendor','service_requests.vehicle_id as vehicle_id', 'service_requests.location_id as location_id' 
                                                ,'orders.user_id as _userID','orders.service_request_id as service_request_id','orders.service_id as service_id','orders.service_cat_id as service_cat_id', 
                                                'orders.paid_status as paid_status', 'orders.payment_through as payment_through','services.type as service_type' , 'services.type as _type' ,  
                                                'services.service_duration as service_duration','service_requests.updated_at as serv_updated_at','users.name as user_name','coupons.name as coupon_name','coupons.amount as coupon_amount','coupons.type as coupon_type')
                                    ->distinct()
                                    ->rightJoin('services','orders.service_id','=','services.id')
                                    ->rightJoin('users','orders.user_id','=','users.id')
                                    //->rightJoin('coupons','orders.coupon_id','=','coupons.id')
                                    ->leftJoin('coupons',function($join){
                                        $join
                                        ->on('orders.coupon_id','=','coupons.id');
                                        //->orWhereNull('orders.coupon_id');
                                      })
                                    ->rightJoin('service_requests','orders.service_request_id','=','service_requests.id')
                                    ->where('service_requests.vendor_id','=',$_userId)
                                    ->where('service_requests.status','=',$_status)
                                    ->get()
                                    ->toArray();
            }
            
        }

        $_activeOrder     =  Order::select('orders.id', 'services.service_title','orders.created_at','orders.updated_at','orders.status','orders.status as order_status','service_requests.status as service_status', 'services.icon_image as _image', 'orders.order_amount as _amount',
                                                'service_requests.vendor_id as _vendor','service_requests.vehicle_id as vehicle_id', 'service_requests.location_id as location_id' 
                                                ,'orders.user_id as _userID','orders.service_request_id as service_request_id','orders.service_id as service_id','orders.service_cat_id as service_cat_id', 
                                                'services.type as _type' ,  'services.service_duration as service_duration','service_requests.updated_at as serv_updated_at','users.name as user_name','coupons.name as coupon_name','coupons.amount as coupon_amount','coupons.type as coupon_type')
                                ->distinct()
                                ->rightJoin('services','orders.service_id','=','services.id')
                                ->rightJoin('users','orders.user_id','=','users.id')
                                ->rightJoin('service_requests','orders.service_request_id','=','service_requests.id')
                                //->rightJoin('coupons','orders.coupon_id','=','coupons.id')
                                ->leftJoin('coupons',function($join){
                                    $join
                                    ->on('orders.coupon_id','=','coupons.id');
                                    //->orWhereNull('orders.coupon_id');
                                  })
                                ->where('service_requests.vendor_id','=',$_userId)
                                //->where('service_requests.accepted_by','!=',null)
                                ->orderBy('created_at', 'desc')
                                ->where('orders.status','=','processing')
                                ->whereIn('service_requests.status',[0,3,4,2])
                                ->get()
                                ->toArray();
       
        $_address   =   [];
        $vehicle    =   [];
        $__activeOrder       =   [];
        if( !empty($_activeOrder) ){
            foreach( $_activeOrder as $a_k => $a_d ){
                $__activeOrder[$a_k]['_order'] = $a_d; 
                $vehicle     = CustomerVehicle::select('vehicles.vehicle_year as year','vehicle_makes.name as make','vehicle_transmissions.name as transmission','vehicle_classes.name as classes','vehicle_model.name as model',
                                                        'vehicles.label as label','vehicles.vehicle_country as _countryID', 'vehicles.vehicle_class	 as _classID',
                                                        'vehicles.km_run as km', 'vehicles.fuel_type as fuel_type','vehicles.id as _vid')
                                                        ->distinct()
                                                        ->rightJoin('vehicle_makes','vehicles.vehicle_make','=','vehicle_makes.id')
                                                        ->rightJoin('vehicle_transmissions','vehicles.vehicle_transmission','=','vehicle_transmissions.id')
                                                        ->rightJoin('vehicle_classes','vehicles.vehicle_class','=','vehicle_classes.id')
                                                        //->rightJoin('countries','vehicles.vehicle_country','=','countries.id')
                                                        //->rightJoin('vehicle_displacements','vehicles.vehicle_displacement','=','vehicle_displacements.id')
                                                        //->rightJoin('vehicle_drives','vehicles.vehicle_drive','=','vehicle_drives.id')
                                                        ->rightJoin('vehicle_model','vehicles.vehicle_model','=','vehicle_model.id')
                                                        ->where('vehicles.user_id',$a_d['_userID'])
                                                        ->where('vehicles.id', $a_d['vehicle_id'])
                                                        //->where('vehicles.active',1)
                                                        ->get()
                                                        ->toArray();
                $__activeOrder[$a_k]['_vehicle'] = $vehicle;

                $_address       =  Location::select('locations.*', 'vendor_locations.name as _area','locations.id as _lID')
                                                ->distinct()
                                                ->where('user_id', '=', $a_d['_userID'])
                                                ->rightJoin('vendor_locations','locations.area','=','vendor_locations.id')
                                                //->where('active',1)
                                                ->where('locations.id', $a_d['location_id'])
                                                ->get()->toArray(); 
                $__activeOrder[$a_k]['_location'] = $_address;

                $__vendorList        =   UsersMeta::select('users.id as _id','users.name as vendor_name','users.image as _image','users.service_duration as _duration')
                                                ->distinct()
                                                ->rightJoin('users','users_meta.user_id','=','users.id')
                                                ->where('users.id',$a_d['_vendor'])
                                                ->get()->toArray();
                $__activeOrder[$a_k]['_vendor'] = $__vendorList;
            }
             
        }


        $_data    = [];
        $_vendorList    =   [];
        if( !empty($_order) ){
            foreach( $_order as $_k => $_d ){
                //print_r( $_d);
                $_data[$_k]['_order'] = $_d;

                $__vehicle     = CustomerVehicle::select('vehicles.vehicle_year as year','vehicle_makes.name as make','vehicle_transmissions.name as transmission','vehicle_classes.name as classes','vehicle_model.name as model',
                                                'vehicles.label as label','vehicles.vehicle_country as _countryID', 'vehicles.vehicle_class	 as _classID',
                                                'vehicles.km_run as km', 'vehicles.fuel_type as fuel_type','vehicles.id as _vid')
                                                ->distinct()
                                                ->rightJoin('vehicle_makes','vehicles.vehicle_make','=','vehicle_makes.id')
                                                ->rightJoin('vehicle_transmissions','vehicles.vehicle_transmission','=','vehicle_transmissions.id')
                                                ->rightJoin('vehicle_classes','vehicles.vehicle_class','=','vehicle_classes.id')
                                                //->rightJoin('countries','vehicles.vehicle_country','=','countries.id')
                                                //->rightJoin('vehicle_displacements','vehicles.vehicle_displacement','=','vehicle_displacements.id')
                                                //->rightJoin('vehicle_drives','vehicles.vehicle_drive','=','vehicle_drives.id')
                                                ->rightJoin('vehicle_model','vehicles.vehicle_model','=','vehicle_model.id')
                                                ->where('vehicles.user_id',$_d['_userID'])
                                                ->where('vehicles.id', $_d['vehicle_id'])
                                                //->where('vehicles.active',1)
                                                ->get()
                                                ->toArray();
                $_data[$_k]['_vehicle'] = $__vehicle;

                $__address       =  Location::select('locations.*', 'vendor_locations.name as _area','locations.id as _lID')
                                                ->distinct()
                                                ->where('user_id', '=', $_d['_userID'])
                                                ->rightJoin('vendor_locations','locations.area','=','vendor_locations.id')
                                                //->where('active',1)
                                                ->where('locations.id', $_d['location_id'])
                                                ->get()->toArray(); 
                $_data[$_k]['_location'] = $__address;

                $__vendorList        =   UsersMeta::select('users.id as _id','users.name as vendor_name','users.image as _image','users.service_duration as _duration')
                                            ->distinct()
                                            ->rightJoin('users','users_meta.user_id','=','users.id')
                                            ->where('users.id', $_userId)
                                            ->get()->toArray();

                $_data[$_k]['_vendor'] = $__vendorList;

            }
        }
        
        // $_servicesCat      = ServiceCategory::orderBy('cat_name', 'ASC')
        //                             ->where('parent_id','=', null)
        //                             ->get()->toArray();

        // if( !empty($_servicesCat) ){
        //     foreach( $_servicesCat as $key => $data ){
        //         $resposne[$key]['id']                     =   $data['id'];
        //         $resposne[$key]['title']                  =   $data['cat_name'];
        //         //$resposne[$key]['sub_service']            =   $this->getSubServiceCategory($data['id']);
        //     }
        // }

        $orderRequest   =   [];
        if( $_req !='' && $_req =='order_req' ){
           
            $userRequest    = UserRequest::select('user_request.*', 'services.service_title as _title', 'services.id as _serviceid', 'services.icon_image as _image')
                                ->leftJoin('services','user_request.service_id','=','services.id')
                                ->where('user_request.created_at', '<', Carbon::now()->addMinutes(2)->toDateTimeString())
                                ->where('user_request.accepted_by','=',null)
                                ->where('user_request.rejected_by','=',null)
                                ->get()
                                ->toArray();
            //print_r($userRequest);
            if( !empty($userRequest) ){

                foreach( $userRequest as $_uKey => $_uData ){
                    $orderRequest[$_uKey]['request']   =  $_uData;

                    $__vehicle     = CustomerVehicle::select('vehicles.vehicle_year as year','vehicle_makes.name as make','vehicle_model.name as model')
                                                ->distinct()
                                                ->rightJoin('vehicle_makes','vehicles.vehicle_make','=','vehicle_makes.id')
                                                ->rightJoin('vehicle_transmissions','vehicles.vehicle_transmission','=','vehicle_transmissions.id')
                                                ->rightJoin('vehicle_classes','vehicles.vehicle_class','=','vehicle_classes.id')
                                                ->rightJoin('vehicle_model','vehicles.vehicle_model','=','vehicle_model.id')
                                                ->where('vehicles.user_id',$_uData['user_id'])
                                                ->where('vehicles.active',1)
                                                ->get()
                                                ->toArray()[0];
                    $orderRequest[$_uKey]['_vehicle']   =  $__vehicle;

                    $__address       =  Location::select('locations.block as _block', 'vendor_locations.name as _area','locations.id as _id')
                                                ->distinct()
                                                ->where('user_id', '=', $_uData['user_id'])
                                                ->rightJoin('vendor_locations','locations.area','=','vendor_locations.id')
                                                ->where('active',1)
                                                ->get()->toArray()[0]; 
                    $orderRequest[$_uKey]['_location'] = $__address;

                }

            }

        }
        //echo Carbon::now()->subMinutes(2)->toDateTimeString();
        //print_r( $orderRequest);

        if( !empty($_order) || !empty($orderRequest) ){
            return response()->json([
                'status'        => true,
                'message'       => 'Success',
                'data'          => $_data,
                'active_order'  =>  $__activeOrder,
                //'_category'     => $resposne,
                'order_request' => $orderRequest
               // '_location'     =>  $_address,
               // '_vehicle'      =>  $vehicle
            ],200);
        }else{
            return response()->json([
                'status'        => true,
                'message'       => 'No data',
                'data'          => [],
                'active_order'  => [],
                '_category'     => []
            ],200);
        }

    }


    public function updateOrder( Request $request , $id ){

    }
}
