<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\Location;
use App\Models\CustomerVehicle;
use App\Models\Review;
use App\Models\UsersMeta;
use Carbon\Carbon;

class TransactionController extends Controller
{
    //

    public static function createPaymentUrl( $orderData = [] ){

        $createEndpoint     = '/api/v2/web-ven-sdd/epayment/create/';

        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL                     => ( env("APP_ENV") !='development' ) ? env("SADAD_LIVE_URL").$createEndpoint : env("SADAD_SANDBOX_URL").$createEndpoint,// your preferred url
            CURLOPT_RETURNTRANSFER          => true,
            CURLOPT_ENCODING                => "",
            CURLOPT_MAXREDIRS               => 10,
            CURLOPT_TIMEOUT                 => 30000,
            CURLOPT_HTTP_VERSION            => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST           => "POST",
            CURLOPT_POSTFIELDS              => json_encode($orderData),
            CURLOPT_HTTPHEADER              => array(
                                                    // Set here requred headers
                                                    "accept: */*",
                                                    "content-type: application/json",
                                                ),
        ));
        
        $response           = curl_exec($curl);
        $err                = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
            return $err;
        } else {
          return json_decode($response);
        }
    }

    public function paymentSuccess( Request $request, $id, $user ){
      //  print_r($request->all());
    
        $fcmToken        = User::where('id',$user)
                            ->where('status','!=',0)
                            ->pluck('fcm_token')
                            ->toArray();
        //$this->senPushNotification(array_filter($fcmToken),'','','','',$id);exit;
        if(  $request->ResultCode ==0 &&  $request->ResultMessage =='CAPTURED' ){

            $_Orderupdate =    Order::updateOrInsert(
                ['id' => $id],
                [
                    'paid_status'   =>   'paid',
                    'status'        =>   'processing',
                    'updated_at' => Carbon::now()
                ]
            );
            if( $_Orderupdate ){
                
                $_transUpdate =    Transaction::updateOrInsert(
                    ['transaction_ref' => $request->TransactionIdentifier, 'order_id' => $id],
                    [
                        'result'            =>   $request->ResultCode,
                        'result_message'    =>   $request->ResultMessage,
                        'transaction_type'  =>   $request->TransactionType,
                        'order_id_sadad'    =>   $request->OrderId,
                    ]
                );
                $this->senPushNotification(array_filter($fcmToken),'','','','',$id);
                if(  $_transUpdate ){
                    // return response()->json([
                    //     'status'        => true,
                    //     'message'       => 'Transaction done successfully!!!',
                    // ],200);
                    return view('frontend.success', ['success' => $request->all()]);
                }
            }
   
        }else{
            
            return view('frontend.success', ['success' => $request->all()]);

            // return response()->json([
            //     'status'        => false,
            //     'message'       => 'Payent not captured!!!',
            // ],200);
        }

       
    }

    public function paymentError( Request $request, $id, $reqid){
       // print_r($request->all());

        if(  $request->ResultCode !=0  ){

            $_Orderupdate =    Order::updateOrInsert(
                                    ['id' => $id],
                                    [
                                        'status'        =>   'cancel',
                                    ]
                                );
            $_ServiceRequpdate = ServiceRequest::updateOrInsert(
                                    ['id' => $reqid],
                                    [
                                        'status' =>   1,
                                    ]
                                );
            $_transUpdate =    Transaction::updateOrInsert(
                ['transaction_ref' => $request->TransactionIdentifier, 'order_id' => $id],
                [
                    'result'            =>   $request->ResultCode,
                    'result_message'    =>   $request->ResultMessage,
                    'transaction_type'  =>   $request->TransactionType,
                    'order_id_sadad'    =>   $request->OrderId,
                ]
            );
            if(  $_transUpdate ){
                // return response()->json([
                //     'status'        => false,
                //     'message'       => $request->ResultMessage,
                // ],200);
                return view('frontend.error', ['error' => $request->all()]);
            }
   
        }

    }

    private function senPushNotification( $token = [], $_year = '', $service = '' , $id ='' , $_date = '', $order_id = '' ){
            
        //  $SERVER_API_KEY = "AAAABnKch4w:APA91bEeYiDRzKvbvsYbw6mq_uVYm59It7auG1pQBg6-K0_oZDtihauW32w2bKvME-5MwD6xqsW4T5j-x9tk8k5BtJVH5i9p4iisQkU-m3ZkwVb0d2cqR0W0qBR_nFzhEBL9iboqu7OK";
          $ordrData         =   $this->getOrderDetail( $order_id);
          $SERVER_API_KEY   = env('VENDOR_SERVER_API_KEY');
       //   echo $SERVER_API_KEY;exit;
          $data = [
              "registration_ids" => $token,
              "notification" => [
                  "title"                 =>  'New Order',
                  "body"                  =>  'Hello, you have a new order #'.$order_id,  
                  "priority"              =>  "high",
                  "content_available"     =>  true,
              ],
              "data"  => [
                  "screen"                => "Order_Detail",
                  "priority"              => "high",
                  "_dorder"               => $ordrData['_dorder'],
                  "_dvehicle"             => $ordrData['_dvehicle'],
                  "_dlocation"            => $ordrData['_dlocation'],
                  "_dvendor"              => $ordrData['_dvendor'],
                  "_id"                   => $order_id,
              ]
          ];

    

        //print_r($data);exit;
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

    private function getOrderDetail( $order_id = null ){

        $_order      =  Order::select('orders.id', 'services.service_title','orders.created_at','orders.updated_at','orders.status','orders.status as order_status','service_requests.status as service_status', 'services.icon_image as _image', 'orders.order_amount as _amount',
                                            'service_requests.vendor_id as _vendor','service_requests.vehicle_id as vehicle_id', 'service_requests.location_id as location_id' , 
                                            'service_requests.service_id as service_id' , 'orders.paid_status as paid_status', 'orders.payment_through as payment_through','services.type as service_type',  
                                            'services.service_duration as service_duration','service_requests.updated_at as serv_updated_at','users.name as user_name', 'services.type as _type',
                                            'users.id as _userID','orders.service_request_id as service_request_id' ,'coupons.name as coupon_name','coupons.amount as coupon_amount','coupons.type as coupon_type')
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
                                ->where('orders.id','=',$order_id)
                                ->orderBy('created_at', 'desc')
                                ->get()
                                ->toArray();
        $_data    = [];
        if( !empty($_order) ){
            foreach( $_order as $_k => $_d ){
                //print_r( $_d);
                $_data['_dorder'] = $_d;

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
                                                //->where('vehicles.user_id',$_userId)
                                                ->where('vehicles.id', $_d['vehicle_id'])
                                                // ->where('vehicles.active',1)
                                                ->get()
                                                ->toArray();
                $_data['_dvehicle'] = $__vehicle;

                $__address       =  Location::select('locations.*', 'vendor_locations.name as _area','locations.id as _lID')
                                                ->distinct()
                                                //->where('user_id', '=', $_userId)
                                                ->rightJoin('vendor_locations','locations.area','=','vendor_locations.id')
                                                //->where('active',1)
                                                ->where('locations.id', $_d['location_id'])
                                                ->get()->toArray(); 
                $_data['_dlocation'] = $__address;

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

                        $_data['_dvendor'] = [
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
        return $_data;
       // print_r($_data[0]);exit;
    }

}
