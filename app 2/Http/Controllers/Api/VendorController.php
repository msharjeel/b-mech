<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsersMeta;
use Illuminate\Support\Arr;
use App\Models\UserRequest;
use App\Models\UserRequestLog;
use App\Models\CustomerVehicle;
use App\Models\Location;
use App\Models\Service;
use App\Models\Review;
use App\Models\ServiceRequest;

class VendorController extends Controller
{
    //
    public function getAllVendors( Request $request , $id = null ){

        $_vendorIds      =  $request->json()->all();
        $_response       = [];
        $_fcmtoken       = [];
        $selectVendor    = [];
        //echo $request->year;
        $request->validate([
            'service_id'            =>  'required',
            'country_id'            =>  'required',
            'vehicle_class_id'      =>  'required',
            'year'                  =>  'required',
            'location'              =>  'required'
        ]);
        $search_term    = $request->input('sq');
        if($search_term){
            // $_vendorList        =   UsersMeta::select('users.id as _id','users.name as vendor_name','users.image as _image','users_meta.amount as _amount',
            //                                     'users.description as _description', 'users.service_duration as _duration',
            //                                     'users.location as _location' , 'users.vendor_location_range as _range' , 'users.latitude as _latitude', 
            //                                     'users.longitude as _longitude', 'users.b_mechanic_comission as _bcomission', 'users.fcm_token as fcm_token')
            //                                     ->distinct()
            //                                     ->rightJoin('users','users_meta.user_id','=','users.id')
            //                                     ->where('users_meta.service_id',$request->service_id)
            //                                     ->where('users_meta.country_id',$request->country_id)
            //                                     ->where('users_meta.vehicle_class_id',$request->vehicle_class_id)
            //                                     ->where('users.name', 'LIKE', '%'.$search_term.'%')
            //                                     ->get();
            return response()->json([
                'status'        => true,
                'message'       => 'No data',
                'data'          => $_response,
            ],200);
        }else{
            $_vendorList        =   UsersMeta::select('users.id as _id','users.name as vendor_name','users.image as _image','users_meta.amount as _amount',
                                                'users.description as _description', 'users.service_duration as _duration',
                                                'users.location as _location' , 'users.vendor_location_range as _range' , 'users.latitude as _latitude', 
                                                'users.longitude as _longitude', 'users.b_mechanic_comission as _bcomission', 'users.fcm_token as fcm_token')
                                                ->distinct()
                                                ->rightJoin('users','users_meta.user_id','=','users.id')
                                                ->where('users_meta.service_id',$request->service_id)
                                                ->where('users_meta.country_id',$request->country_id)
                                                ->where('users_meta.vehicle_class_id',$request->vehicle_class_id)
                                                ->where('users.status', 1)
                                                ->get();
        }

        //print_r($_vendorList);

        // $catName            =   Service::getCatID($request->service_id);
        // echo $catName;
        // exit;
        $locationLatLong    = Location::where('user_id', $id)
                                        ->where('active',1)
                                        ->select('latitude','longitude')
                                        ->get()->toArray();
        if( !empty($locationLatLong[0]) ){
            $locationLatLong = $locationLatLong[0];
        }
       // print_r($locationLatLong);exit;

        // $collection         = collect($_vendorList);
        // $plucked            = $collection->pluck('fcm_token');
        // $_fcmtoken          = array_filter($plucked->all());

        // if( !empty( $_fcmtoken ) ){
        //     $Userrreq = new UserRequest;
        //     $Userrreq->service_id   = $request->service_id;
        //     $Userrreq->user_id      = $id;
        //     $this->senPushNotification(array_filter($_fcmtoken),  $request->year,$request->service_id, $id);

        //     if($Userrreq->save()){ 
        //         $k = 1;
        //         for ($i=0; $i <= 2; $i++) {
        //             //echo $k;
        //             $selectVendor  = UserRequest::where('service_id',$request->service_id)->where('user_id',$id)->pluck('accepted_by')->toArray()[0];
        //             if( $k ==2 && !empty($selectVendor) ){
        //                 UserRequest::where('service_id',$request->service_id)->where('user_id',$id)->delete();
        //                 break;
        //             }else if( $k ==2 ){
        //                 UserRequest::where('service_id',$request->service_id)->where('user_id',$id)->delete();
        //                 return response()->json([
        //                     'status'        => true,
        //                     'message'       => 'No one accepted',
        //                     'data'          => [],
        //                 ],200);
        //             }
        //             $k++;
        //             sleep(10);
                    
        //         }                
        //     }
        // }

      //$selectVendor  = UserRequest::where('service_id',$request->service_id)->where('user_id',$id)->pluck('accepted_by')->toArray()[0];
    //  print_r($selectVendor);
    //    exit;
    
    

        if( !empty($_vendorList) ){
            foreach( $_vendorList as $_key => $_data ){
                $_amount            =   json_decode($_data['_amount']);
                $_range             =   json_decode($_data['_range']);
               // print_r($_range);
                $_year              =   $request->year;
                $mapped = Arr::map($_amount, function ( $value,  $key) use ($_year) {                    
                    if( ( $_year >= $value->form_year ) && ( $_year <= $value->to_year )){
                        return $value->price;
                    }
                    
                });

                $reviews        = Review::where('vendor_id',$_data['_id'])
                                    ->sum('rating');
                $reviewsCount   = Review::where('vendor_id',$_data['_id'])
                                    ->count();
              //  $explode                                    =   explode(',',$selectVendor);
               // print_r(array_values(array_filter($mapped)));
                // if(  !empty(array_filter($mapped)) && in_array($request->location,$_range ) && in_array($_data['_id'], $explode) ){
                if(  !empty(array_filter($mapped)) && in_array($request->location,$_range ) ){ 

                    if( !empty($locationLatLong) ){
                        $latitudeFrom   = $locationLatLong['latitude'];
                        $longitudeFrom  = $locationLatLong['longitude'];
                        $latitudeTo     = $_data['_latitude'];
                        $longitudeTo    = $_data['_longitude'];
    
                        $theta      = $longitudeFrom - $longitudeTo;
                        $dist       = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
                        $dist       = acos($dist);
                        $dist       = rad2deg($dist);
                        $miles      = $dist * 60 * 1.1515;
                        $_km        = round($miles * 1.609344, 2).' km';
                    }else{
                        $_km        = '0 Km';
                    }
                    


                    //print_r($explode);exit;
                   // if( in_array($_data['_id'], $explode) ){

                        $_price                                     =   array_values(array_filter($mapped))[0];
                        $_percentage                                =   round(( $_data['_bcomission'] / $_price ) * 100);

                        $total                                                   =   ( $_price + $_percentage );
                        $vat                                                     =   round((  $total / 100 ) * 10, 2 );
                        $total_after_vat                                         =   round($vat + $total);

                        $_response[$_key]['_id']                    = $_data['_id'];
                        $_response[$_key]['_price']                 =  ( !empty(array_filter($mapped)) ) ? array_values(array_filter($mapped))[0] : null;
                        $_response[$_key]['_vendor']                = $_data['vendor_name'];
                        $_response[$_key]['_image']                 = $_data['_image'];
                        $_response[$_key]['_description']           = $_data['_description'];
                        $_response[$_key]['_duration']              = $_data['_duration'];
                        $_response[$_key]['_location']              = $_data['_location'];
                        $_response[$_key]['_latitude']              = $_data['_latitude'];
                        $_response[$_key]['_longitude']             = $_data['_longitude'];
                        $_response[$_key]['_vat_per']               = '10%';
                        $_response[$_key]['_bcomissiom_per']        = $_data['_bcomission'].'%';
                        $_response[$_key]['_bcomission']            = $_percentage;
                        $_response[$_key]['_total']                 = $total;
                        $_response[$_key]['_vat']                   = $vat;
                        $_response[$_key]['_total_after_vat']       = $total_after_vat;
                        $_response[$_key]['_distance']              = $_km;
                        $_response[$_key]['_serviceDone']           = ServiceRequest::where('vendor_id',$_data['_id'])
                                                                                      ->where('status',6)
                                                                                      ->count();
                        $_response[$_key]['_reviews']           =   ( $reviewsCount ) ? round((  $reviews  / $reviewsCount ),1) : 0;
                    
                    //}
                    
                }
               
                
            }
        }
       // print_r($_response);exit;
        if( !empty($_response) ){
            return response()->json([
                'status'        => true,
                'message'       => 'Success',
                'data'          => array_values($_response),
            ],200);
        }else{
            return response()->json([
                'status'        => true,
                'message'       => 'No data',
                'data'          => $_response,
            ],200);
        }
    }

    private function senPushNotification( $token = [], $_year = '', $service = '' , $id ='' , $_date = ''){
            
          //  $SERVER_API_KEY = "AAAABnKch4w:APA91bEeYiDRzKvbvsYbw6mq_uVYm59It7auG1pQBg6-K0_oZDtihauW32w2bKvME-5MwD6xqsW4T5j-x9tk8k5BtJVH5i9p4iisQkU-m3ZkwVb0d2cqR0W0qBR_nFzhEBL9iboqu7OK";
            $serviceTitle   = Service::getServiceName($service);
            $SERVER_API_KEY = env('VENDOR_SERVER_API_KEY');
            $data = [
                "registration_ids" => $token,
                "notification" => [
                    "title"                 =>  'New Request!',
                    "body"                  =>  'You have a new '.$serviceTitle. ' Request.',  
                    "priority"              =>  "high",
                    "content_available"     =>  true,
                ],
                "data"  => [
                    "screen"                => "Vendor Order Detail",
                    "priority"              => "high",
                    "year"                  => $_year,
                    "service_id"            => $service,
                    "user_id"               => $id,
                    "content_available"     => true,
                    "created_date"          => $_date
                ]
            ];
            $dataString = json_encode($data);
        
            $headers = [
                'Authorization: key=' . $SERVER_API_KEY,
                'Content-Type: application/json',
            ];
        
            $ch = curl_init();
        
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
                
            $response = curl_exec($ch);
    
            //dd($response);
    }

    public function getUserReqDetails( Request $request, $id = null ){


        $request->validate([
            'service_id'            =>  'required',
            'user_id'               =>  'required',
            'year'                  =>  'required',
        ]);

        $_vendor_id                 = $id;
        $resposne                   =   [];

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
                                    ->toArray()[0];


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
                                    ->get()->toArray()[0];

        if( !empty($_address) ){
            $resposne['_location'] = $_address;
        }else{
            $resposne['_location'] = 'No active location';
        }

        $_vendorService         = UsersMeta::where('service_id',$request->service_id)->where('user_id', $_vendor_id)->pluck('amount')->toArray();
        $_vendorDuration        = User::where('id',$_vendor_id)->pluck('service_duration')->toArray()[0];
        $_year                  = $request->year;

        $mapped = Arr::map(json_decode($_vendorService[0]), function ( $value,  $key) use ($_year) {     
             
            if( ( $_year >= $value->form_year ) && ( $_year <= $value->to_year )){
                return $value->price;
            }
            
        });
        $resposne['_service']   =   [
            'name'          => Service::getServiceName($request->service_id),
            'price'         => array_values(array_filter($mapped))[0],
            '_image'        => Service::getServiceImage($request->service_id),
            '_duration'     =>   $_vendorDuration
        ];

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

    public function acceptReject( Request $request , $id = null ){

        $request->validate([
            'service_id'            =>  'required',
            'user_id'               =>  'required',
            'action'                =>  'required'
        ]);

        if( $request->action =='accept' ) {
            $_update =    UserRequest::updateOrInsert(
                ['service_id' => $request->service_id, 'user_id' => $request->user_id ],
                [
                    'accepted_by'       =>   $id,
                ]
            );

          

            if($_update){ 
                UserRequestLog::updateOrInsert(
                    ['service_id' => $request->service_id, 'user_id' => $request->user_id ],
                    [
                        'accepted_by'       =>   $id,
                    ]
                );
                return response()->json([
                    'status'        => true,
                    'message'       => 'Success accepted the request!!!',
                    'data'   => [],
                ],200);
            }
        }else{
            $_update =    UserRequest::updateOrInsert(
                ['service_id' => $request->service_id, 'user_id' => $request->user_id ],
                [
                    'rejected_by'       =>   $id,
                ]
            );

            if($_update){ 
                UserRequestLog::updateOrInsert(
                    ['service_id' => $request->service_id, 'user_id' => $request->user_id ],
                    [
                        'rejected_by'       =>   $id,
                    ]
                );
                return response()->json([
                    'status'        => true,
                    'message'       => 'Request rejected!!!',
                    'data'   => [],
                ],200);
            }
        }

    }


    public function userVendorRequest( Request $request , $id = null ){

        // print_r($request->all());exit;
        $request->validate([
            'service_id'            =>  'required',
            'vendor_id'             =>  'required',
            'year'                  =>  'required',
        ]);

        $fcmToken        = User::where('id', $request->vendor_id)
                                ->where('status','!=',0)
                                ->pluck('fcm_token')
                                ->toArray();

        if( !empty( $fcmToken ) ){
            $Userrreq = new UserRequest;
            $Userrreq->service_id   = $request->service_id;
            $Userrreq->year         = $request->year;
            $Userrreq->user_id      = $id;
            $Userrreq->vendor_id    = $request->vendor_id;

            if($Userrreq->save()){ 

                $this->senPushNotification(array_filter($fcmToken),  $request->year,$request->service_id, $id , $Userrreq->created_at->toDateTimeString() );

                $UserrreqLog = new UserRequestLog;
                $UserrreqLog->service_id   = $request->service_id;
                $UserrreqLog->year         = $request->year;
                $UserrreqLog->user_id      = $id;
                $UserrreqLog->vendor_id    = $request->vendor_id;
                $UserrreqLog->save();


                $k = 1;
                for ($i=0; $i <= 9; $i++) {
                    //echo $k;
                    $selectVendor  = UserRequest::where('service_id',$request->service_id)->where('user_id',$id)->pluck('accepted_by')->toArray()[0];
                    $rejectVendor  = UserRequest::where('service_id',$request->service_id)->where('user_id',$id)->pluck('rejected_by')->toArray()[0];
                    if( !empty($selectVendor) ){
                        UserRequest::where('service_id',$request->service_id)->where('user_id',$id)->delete();
                        return response()->json([
                            'status'        => true,
                            'message'       => 'Vendor accepted',
                            'data'          => [
                                'action'    => 'accepted',
                                'vendor'    => $request->vendor_id
                            ],
                        ],200);
                        break;
                    }else if( !empty($rejectVendor) ){
                        UserRequest::where('service_id',$request->service_id)->where('user_id',$id)->delete();
                        return response()->json([
                            'status'        => false,
                            'message'       => 'Vendor Not accepted/Rejected',
                            'data'          => [
                                'action'    => 'rejected',
                                'vendor'    => $request->vendor_id
                            ],
                        ],406);
                        break;
                    }else if( $k ==9 ){
                        UserRequest::where('service_id',$request->service_id)->where('user_id',$id)->delete();
                        return response()->json([
                            'status'        => false,
                            'message'       => 'Request timed out!!!',
                            'data'          => [
                                'action'    => 'timeout',
                                'vendor'    => $request->vendor_id
                            ]
                        ],406);
                        break;
                    }
                    $k++;
                    sleep(10);
    
                }                
            }
        }else{
            return response()->json([
                'status'        => true,
                'message'       => 'Vendor is offline!!!',
                'data'          => [
                    'action'    => 'timeout',
                    'vendor'    => $request->vendor_id
                ]
            ],406);
        }

    }

    public function cancelService( Request $request , $id = null ){

        $cancel  = UserRequest::where('user_id',$id)->delete();
        
        if($cancel){
            return response()->json([
                'status'        => true,
                'message'       => 'Request cancelled successfully',
                'data'          => [],
            ],200);
        }
    }


    public function getHomeVendor(  Request $request  ){

        $search_term    =   $request->input('sq');
        $_vendorList    =   [];
        if($search_term){
            $_vendorList        =   User::select('users.id as _id','users.name as vendor_name','users.image as _image',
                                                'users.description as _description', 'users.service_duration as _duration','users.location as location')
                                                        ->distinct()
                                                        ->leftJoin('users_meta','users.id','=','users_meta.user_id')
                                                        //->leftJoin('reviews','users.id','=','reviews.vendor_id')
                                                ->where('users.name', 'LIKE', '%'.$search_term.'%')
                                                ->whereHas(
                                                            'roles', function($q){
                                                                $q->where('name', 'Vendor');
                                                            }
                                                    )
                                                // ->groupBy('users.id')
                                                // ->havingRaw('count(reviews.rating) > ?', [0])
                                                ->get();
    
        }else{
            $_vendorList        =   User::select('users.id as _id','users.name as vendor_name','users.image as _image',
                                                'users.description as _description', 'users.service_duration as _duration','users.location as location')
                                                        ->distinct()
                                                        ->leftJoin('users_meta','users.id','=','users_meta.user_id')
                                                       // ->leftJoin('reviews','users.id','=','reviews.vendor_id')
                                                ->whereHas(
                                                    'roles', function($q){
                                                        $q->where('name', 'Vendor');
                                                    }
                                                )
                                                // ->groupBy('users.id')
                                                // ->havingRaw('count(reviews.rating) > ?', [0])
                                                ->get();
        }

        if( !empty($_vendorList) ){
            $_vendorHome        =   [];
            foreach($_vendorList as $_kh => $_kv ){

                $reviews        = Review::where('vendor_id',$_kv['_id'])
                                    ->sum('rating');
                $reviewsCount   = Review::where('vendor_id',$_kv['_id'])
                                    ->count();

                $_vendorHome[$_kh]  = [
                    '_id'                   => $_kv['_id'],
                    'vendor_name'           => $_kv['vendor_name'],
                    '_image'                => $_kv['_image'],
                    '_description'          => $_kv['_description'],
                    '_duration'             => $_kv['_duration'],
                    'location'              => $_kv['location'],
                    'review'                =>  ( $reviewsCount ) ? round((  $reviews  / $reviewsCount ),1) : 0,
                ];
            }
            
            return response()->json([
                'status'        => true,
                'message'       => 'Success',
                'data'          => $_vendorHome,
            ],200);
        }else{
            return response()->json([
                'status'        => true,
                'message'       => 'No data',
                'data'          => [],
            ],200);
        }

    }
}
