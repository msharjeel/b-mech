<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Models\CustomerVehicle;
use App\Models\Location;
use App\Models\Order;
use App\Models\User;
use App\Models\UsersMeta;
use App\Models\Banner;
use App\Models\Review;
use App\Models\Notification;

class DashboardController extends Controller
{
    //
    public function index( Request $request ){
      
        $request->validate([
            'user_id'       =>  'required'
        ]);

        $resposne       =   [];
        $vehicle        =   [];
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
                                        ->where('vehicles.user_id',$request->user_id)
                                        ->where('vehicles.active',1)
                                        ->get()
                                        ->toArray();

                                         
        $resposne['_username']  = ( $request->user_id =='guest' ) ? 'Guest' : User::find($request->user_id)->name;

        $banner                 =   Banner::select('banners.id as id', 'banners.image as url')
                                        ->get()
                                        ->toArray();
        
        if( !empty($banner) ){
            $resposne['banner'] = $banner;
        }else{
            $resposne['banner'] = $banner;
        }

        if( !empty($vehicle) ){
            $resposne['_vehicle'] = $vehicle;
        }else{
            $resposne['_vehicle'] = 'No active vehicle';
        }

        $_address       =  Location::select('locations.*', 'vendor_locations.name as _area','locations.id as _lID')
                                    ->distinct()
                                    ->where('user_id', '=', $request->user_id )
                                    ->rightJoin('vendor_locations','locations.area','=','vendor_locations.id')
                                    ->where('active',1)
                                    ->get()->toArray();

        if( !empty($_address) ){
            $resposne['_location'] = $_address;
            $_areaID               = $_address[0]['area'];
            $getVendor = User::query()
                            ->where('vendor_location_range', 'like','%'.$_areaID.'%')->orderBy('lft')
                            ->select('users.id as id','name','description','image')
                            ->leftJoin('reviews','users.id','=','reviews.vendor_id')
                            ->groupBy('users.id')
                            ->havingRaw('count(reviews.rating) > ?', [0])
                            ->get();
            if( !empty($getVendor[0]) ){
                foreach( $getVendor as $_kv => $_dv ){

                    $reviews        = Review::where('vendor_id',$_dv['id'])
                                    ->sum('rating');
                    $reviewsCount   = Review::where('vendor_id',$_dv['id'])
                                        ->count();
                    $resposne['_topvendor'][$_kv] = [
                        'id'            => $_dv['id'],
                        'name'          => $_dv['name'],
                        'description'   => $_dv['description'],
                        'image'         => $_dv['image'],
                        'review'       =>  ( $reviewsCount ) ? round((  $reviews  / $reviewsCount ),1) : 0,
                    ];
                }
                
            }
        }else{
            $resposne['_location'] = 'No active location';
        }

        if( $request->user_id =='guest' ){
            $getVendor = User::query()
                        //->where('vendor_location_range', 'like','%'.$_areaID.'%')->orderBy('lft')
                        ->select('users.id as id','name','description','image')
                        ->leftJoin('reviews','users.id','=','reviews.vendor_id')
                        ->groupBy('users.id')
                        ->havingRaw('count(reviews.rating) > ?', [0])
                        ->get();
            if( !empty($getVendor[0]) ){
            foreach( $getVendor as $_kv => $_dv ){

                $reviews        = Review::where('vendor_id',$_dv['id'])
                                ->sum('rating');
                $reviewsCount   = Review::where('vendor_id',$_dv['id'])
                                    ->count();
                $resposne['_topvendor'][$_kv] = [
                    'id'            => $_dv['id'],
                    'name'          => $_dv['name'],
                    'description'   => $_dv['description'],
                    'image'         => $_dv['image'],
                    'review'       =>  ( $reviewsCount ) ? round((  $reviews  / $reviewsCount ),1) : 0,
                ];
            }

            }
        }

        //$status         = ['0' => 'Pending', '1' => 'Accepted', '2' => 'Rejected','3' => 'In-Progress', '4' => 'On-Hold' , '5' => 'Completed', '6' => 'Delivered'];
        $_order         =  Order::select('orders.id', 'services.service_title','orders.created_at','orders.updated_at','orders.status as order_status','service_requests.status as service_status', 'services.icon_image as _image', 'orders.order_amount as _amount',
                                        'service_requests.vendor_id as _vendor','orders.service_id as service_id', 'orders.paid_status as paid_status', 'orders.payment_through as payment_through' , 
                                         'services.service_duration as service_duration','service_requests.updated_at as serv_updated_at','services.type as _type','users.name as user_name','coupons.name as coupon_name'
                                         ,'coupons.amount as coupon_amount','coupons.type as coupon_type','orders.order_rated as rated')
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
                                ->where('user_id','=',$request->user_id)
                                ->where('orders.status','=','processing')
                                ->where('service_requests.status','!=',1)
                                ->orderBy('created_at', 'desc')
                               // ->where('orders.status','=','processing')
                                ->get()
                                ->toArray();

        //print_r($_order);
        if( !empty($_order) ){
            $resposne['_orders'] = $_order;
        }else{
            $resposne['_orders'] = 'No orders';
        }
        $_vendorList   = [];
        if( !empty($_order) ){
            $_vendorList        =   UsersMeta::select('users.id as _id','users.name as vendor_name','users.image as _image','users.service_duration as _duration','users.latitude as _latitude','users.longitude as _longitude')
                                                    ->distinct()
                                                    ->rightJoin('users','users_meta.user_id','=','users.id')
                                                    ->where('users.id',$_order[0]['_vendor'])
                                                    ->get()->toArray();
        }
      
        if( !empty($_vendorList) ){
            //$resposne['_vendor'] = $_vendorList;
            foreach( $_vendorList as $_kl => $_lv ){

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

                $resposne['_vendor'][] = [
                    'id'            => $_lv['_id'],
                    'vendor_name'   => $_lv['vendor_name'],
                    '_image'        => $_lv['_image'],
                    '_duration'     => $_lv['_duration'],
                    '_reviews'      =>  ( $reviewsCount ) ? round((  $reviews  / $reviewsCount ),1) : 0,
                    '_distance'     =>  $_km ,
                ];
            }

        }else{
            $resposne['_vendor'] = 'No orders';
        }

        $_services      = ServiceCategory::orderBy('lft')
                            ->where('parent_id','=', null)
                            ->where('status','=', 1)
                            ->get()->toArray();

        $_servicesCat      = ServiceCategory::orderBy('lft')
                            ->where('parent_id','=', null)
                            ->where('status','=', 1)
                            ->get()->toArray();

        if( !empty($_services) ){
            foreach( $_services as $key => $data ){
                $resposne['service'][$key]['id']                     =   $data['id'];
                $resposne['service'][$key]['title']                  =   $data['cat_name'];
                $resposne['service'][$key]['sub_service']            =   $this->getSubServiceCategory($data['id']);
                $resposne['service'][$key]['image']                  =   $data['image'];
            }
        }

        if( !empty($_servicesCat) ){
            foreach( $_servicesCat as $_key => $_data ){
                $resposne['serviceCat'][$_key]['id']                     =   $_data['id'];
                $resposne['serviceCat'][$_key]['title']                  =   $_data['cat_name'];
                $resposne['serviceCat'][$_key]['sub_service']            =   $this->getSubServiceCategory($_data['id']);
                $resposne['serviceCat'][$_key]['image']                  =   $_data['image'];
            }
        }

        $_notification = Notification::whereJsonContains('users',"$request->user_id")
                            ->get()->toArray();
        
        if( !empty($_notification) ){
            $resposne['_notifications'] = true;
        }else{
            $resposne['_notifications'] = false;
        }
        if( !empty($resposne) ){
            return response()->json([
                'status'        => true,
                'message'       => 'Success',
                'data'   => $resposne,
            ],200);
        }else{
            return response()->json([
                'status'        => true,
                'message'       => 'No data',
                'data'   => $resposne,
            ],200);
        }
    }

    public function getVendorDashboard( Request $request ){

        $request->validate([
            'user_id'       =>  'required'
        ]);

        $resposne       =   [];

        $_servicesCat      = ServiceCategory::orderBy('cat_name', 'ASC')
                            ->where('parent_id','=', null)
                            ->get()->toArray();

        if( !empty($_servicesCat) ){
            foreach( $_servicesCat as $_key => $_data ){
                $resposne['serviceCat'][$_key]['id']                     =   $_data['id'];
                $resposne['serviceCat'][$_key]['title']                  =   $_data['cat_name'];
                $resposne['serviceCat'][$_key]['sub_service']            =   $this->getSubServiceCategory($_data['id']);
                $resposne['serviceCat'][$_key]['image']                  =   $_data['image'];
            }
        }

        if( !empty($resposne) ){
            return response()->json([
                'status'        => true,
                'message'       => 'Success',
                'data'   => $resposne,
            ],200);
        }else{
            return response()->json([
                'status'        => true,
                'message'       => 'No data',
                'data'   => $resposne,
            ],200);
        }

    }

    private function getSubServiceCategory( $parentId ){

        $_subServices      = ServiceCategory::orderBy('cat_name', 'ASC')
                                ->where('parent_id','=', $parentId)
                                ->pluck('cat_name')->toArray();

        return ( !empty($_subServices) ) ? $_subServices : $_subServices;
    }
}
