<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Models\Order;
use App\Models\Service;
use App\Http\Controllers\Api\TransactionController;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Location;
use App\Models\CustomerVehicle;
use App\Models\Review;
use App\Models\UsersMeta;

class ServiceRequestController extends Controller
{
    //
    public function requestService( Request $request , $id ){

        $orderData          =   [];
        $_Order             =   '';
        $transactionData    =   [];
        $request->validate([
            //'user_id'       =>  'required',
            'vendor_id'     =>  'required',
            'service_id'    =>  'required',
            'vehicle_id'    =>  'required',
            'location_id'   =>  'required',
            'status'        =>  'required',
            'payment_type'  =>  'required',
            'amount'        =>  'required',
            'vat'           =>  'required'
        ]);
      
        $_serviceRequest    =   new ServiceRequest([
            'customer_id'       =>   $id,
            'vendor_id'         =>   $request->vendor_id,
            'service_id'        =>   $request->service_id,
            'service_cat_id'    =>   $request->service_cat_id,
            'vehicle_id'        =>   $request->vehicle_id,
            'location_id'       =>   $request->location_id,
            'status'            =>   $request->status,
        ]);


        $fcmToken        = User::where('id', $request->vendor_id)
                                ->where('status','!=',0)
                                ->pluck('fcm_token')
                                ->toArray();
              
      //  print_r($fcmToken);exit;
      
        if($_serviceRequest->save()){

            $catID           =   Service::getCatID($request->service_id);
            
            $_serviceRId    =    $_serviceRequest->id;
            $orderData['service_request_id']    =   $_serviceRId;
            $orderData['service_id']            =   $request->service_id;
            $orderData['user_id']               =   $id;
            $orderData['order_amount']          =   $request->amount;
            $orderData['vat_percentage']        =   $request->vat_percentage;
            $orderData['vat_amount']            =   $request->vat;
            $orderData['paid_status']           =   'un-paid';
            $orderData['payment_through']       =   $request->payment_type;
            $orderData['status']                =   'pending';
            $orderData['b_mechanic']            =   $request->b_mechanic;
            $orderData['service_cat_id']        =   $catID;
            $orderData['coupon_id']             =  ( $request->has('coupon_id') ) ?$request->coupon_id : NULL ;
            
            $_Order         =   $this->createOrder($orderData);

            if( $request->payment_type =='cod' ){
                
                $_update = Order::where('id',  $_Order)
                            ->update(['status' => 'processing','updated_at' => Carbon::now() ]);
                $this->senPushNotification(array_filter($fcmToken),'','','','',$_Order);
                return response()->json([
                    'status'        => true,
                    'message'       => 'Order/Request created successfuly!',
                    'order_id'      => $_Order,
                    'payment'       => []
                ],200);
                
            }else if(  $request->payment_type =='creditcard/debitcard'){

                $userData           =   User::where('id',$id)->select('name','email')
                                            ->get()
                                            ->toArray()[0];

                                           
                if( env("APP_ENV") =='development' ) {

                    $transactionData  = [
                        'api-key'                   => env('SADAD_SANDBOX_API_KEY'),
                        'vendor-id'                 => env('SADAD_SANDBOX_VENDOR_ID'),
                        'branch-id'                 => env('SADAD_SANDBOX_BRANCH_ID'),
                        'terminal-id'               => env('SADAD_SANDBOX_TERMINAL_ID'),
                        'notification-mode'         => 300,
                        "success-url"               =>  route('payment-success', ['id' => $_Order, 'user' => $request->vendor_id]),
                        "error-url"                 =>  route('payment-error', ['id' => $_Order,'reqid' => $_serviceRId]),
                        "description"               =>  'Service',
                        "date"                      => Carbon::now()->toDateTimeString(),
                        "email"                     => $userData['email'],
                        "customer-name"             => $userData['name'],
                        "amount"                    => $request->amount
                    ];


                }else if( env("APP_ENV") =='production' ){

                    $transactionData  = [
                        'api-key'                   => env('SADAD_LIVE_API_KEY'),
                        'vendor-id'                 => env('SADAD_LIVE_VENDOR_ID'),
                        'branch-id'                 => env('SADAD_LIVE_BRANCH_ID'),
                        'terminal-id'               => env('SADAD_LIVE_TERMINAL_ID'),
                        'notification-mode'         => 300,
                        "success-url"               =>  route('payment-success/', ['id' => $_Order]),
                        "error-url"                 =>  route('payment-error/', ['id' => $_Order]),
                        "description"               =>  'Service',
                        "date"                      => Carbon::now()->toDateTimeString(),
                        "email"                     => $userData['email'],
                        "customer-name"             => $userData['name'],
                        "amount"                    => $request->amount
                    ];

                }
                
               
               $onlineTransaction   =  TransactionController::createPaymentUrl($transactionData);
               ///print_r( $onlineTransaction);exit;

                if( isset($onlineTransaction->status) && $onlineTransaction->status ==0 ){

                    $transactionInitial                         =   new Transaction();
                    $transactionInitial->transaction_ref        =   $onlineTransaction->{'transaction-reference'};
                    $transactionInitial->order_id               =   $_Order;
                    $transactionInitial->status                 =   $onlineTransaction->status;
                    $transactionInitial->error_code             =   $onlineTransaction->{'error-code'};
                    $transactionInitial->error_message	        =   $onlineTransaction->{'error-message'};
                    $transactionInitial->invoice_id	            =   $onlineTransaction->{'invoice-id'};
                    //$this->senPushNotification(array_filter($fcmToken),'','','','',$_Order);
                    if( $transactionInitial->save() ){
                        return response()->json([
                            'status'        => true,
                            'message'       => 'Order/Request created successfuly!',
                            'order_id'      => $_Order,
                            'payment'       => $onlineTransaction
                        ],200);
                    }
    
                }else{
                    return response()->json([
                        'status'        => false,
                        'message'       => 'Issue on online payment',
                        'order_id'      => $_Order,
                        'payment'       => []
                    ],200);
                }
                

            }

            

        }else{
            return response()->json([
                'status'        => true,
                'message'       => 'Error, contact administrator',
            ],200);
        }

    }

    private function createOrder( $data ){
       // print_r($data);exit;
        $_orderCreate                =   new Order([
            'service_request_id'        =>   $data['service_request_id'],
            'service_id'                =>   $data['service_id'],
            'user_id'                   =>   $data['user_id'],
            'order_amount'              =>   $data['order_amount'],
            'vat_amount'                =>   $data['vat_amount'],
            'vat_percentage'            =>   $data['vat_percentage'],
            'paid_status'               =>   $data['paid_status'],
            'payment_through'           =>   $data['payment_through'],
            'status'                    =>   $data['status'],
            'b_mechanic'                =>   $data['b_mechanic'],
            'service_cat_id'            =>   $data['service_cat_id'],
            'coupon_id'                 =>   $data['coupon_id']
        ]);

        if($_orderCreate->save()){

            $orderId    =   $_orderCreate->id;
            return $orderId;

        }
    }

    public function updateRequestStatus( Request $request ){

        $request->validate([
            'request_id'        =>  'required',
            'status'            =>  'required',
        
        ]);
      
       $_update = ServiceRequest::where('id',  $request->request_id)
            ->update(['status' => $request->status,'updated_at' => Carbon::now() ]);

        // if( $request->status == '6' ){
        //    // echo 'asd';
        //     $_Orderupdate = Order::where('service_request_id',  $request->request_id)
        //                         ->update(['status' => 'complete','updated_at' => Carbon::now() ]);
        // }
      

        // $_update =    ServiceRequest::updateOrCreate(
        //     ['id' => $request->request_id ],
        //     [
        //         'status'     =>   $request->status,
        //         'updated_at' => Carbon::now()
        //     ]
        // );
        if($_update){
        
            return response()->json([
                        'status'        => true,
                        'message'       => 'Status updated successfuly!',
                    ],200);
        }
        else{
            return response()->json([
                'status'        => false,
                'message'       => 'Error, check administrator!',
            ],200);
        }
    }


    private function senPushNotification( $token = [], $_year = '', $service = '' , $id ='' , $_date = '', $order_id = '' ){
            
        //  $SERVER_API_KEY = "AAAABnKch4w:APA91bEeYiDRzKvbvsYbw6mq_uVYm59It7auG1pQBg6-K0_oZDtihauW32w2bKvME-5MwD6xqsW4T5j-x9tk8k5BtJVH5i9p4iisQkU-m3ZkwVb0d2cqR0W0qBR_nFzhEBL9iboqu7OK";
          $ordrData         =   $this->getOrderDetail( $order_id);
           // print_r(  $ordrData  );
          $SERVER_API_KEY   = env('VENDOR_SERVER_API_KEY');
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
                                            'service_requests.vendor_id as _vendor','service_requests.vehicle_id as vehicle_id', 'service_requests.location_id as location_id' , 'service_requests.service_id as service_id' , 
                                            'orders.paid_status as paid_status', 'orders.payment_through as payment_through','services.type as service_type',  'services.service_duration as service_duration',
                                            'service_requests.updated_at as serv_updated_at','users.name as user_name', 'services.type as _type','users.id as _userID','orders.service_request_id as service_request_id' ,
                                            'coupons.name as coupon_name','coupons.amount as coupon_amount','coupons.type as coupon_type')
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
