<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\ServiceRequest;
use Carbon\Carbon;
use App\Models\Order;
class ServiceController extends Controller
{
    //
    public function getServiceCategories( Request $request ){

        $resposne       =   [];
        $_services      = ServiceCategory::orderBy('lft')
                            ->where('parent_id','=', null)
                            ->where('status','=', 1)
                            ->get()->toArray();

        if( !empty($_services) ){
            foreach( $_services as $key => $data ){
                $resposne[$key]['id']                     =   $data['id'];
                $resposne[$key]['title']                  =   $data['cat_name'];
                $resposne[$key]['sub_service']            =   $this->getSubServiceCategory($data['id']);
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

        $_subServices      = ServiceCategory::orderBy('lft')
                                ->where('parent_id','=', $parentId)
                                ->where('status','=', 1)
                                ->pluck('cat_name')->toArray();

        return ( !empty($_subServices) ) ? $_subServices : $_subServices;
    }

    public function getService( Request $request ){

        $request->validate([
            'service_id'     =>  'required',
        ]);

        $search_term    = $request->input('_sq');;
        if($search_term &&  $request->service_id !='all' ){
            $_servicesList     =   Service::orderBy('lft')->where('services.service_id',$request->service_id)
                                        ->distinct()
                                        ->where('services.service_id',$request->service_id)
                                        ->leftJoin('favourites','services.id','=','favourites.service_id')
                                        ->where('services.service_title', 'LIKE', '%'.$search_term.'%')
                                        ->where('services.status','=', 1)
                                        ->orderBy('services.service_title', 'ASC')
                                        ->get();
        }else{
            if( $request->service_id !='all' ){
                $_servicesList     =   Service::orderBy('lft')->select('services.*', 'favourites.id as _favid')
                                        ->distinct()
                                        ->where('services.service_id',$request->service_id)
                                        ->where('services.status','=', 1)
                                        ->leftJoin('favourites','services.id','=','favourites.service_id')
                                        ->orderBy('services.service_title', 'ASC')
                                        ->get();
            }else{
                if($search_term){
                    $_servicesList     =   Service::orderBy('lft')->select('services.*', 'favourites.id as _favid')
                                            ->distinct()
                                            ->leftJoin('favourites','services.id','=','favourites.service_id')
                                            ->where('services.status','=', 1)
                                            ->where('services.service_title', 'LIKE', '%'.$search_term.'%')
                                            ->orderBy('services.service_title', 'ASC')
                                            ->get();

                }else{
                    $_servicesList     =   Service::orderBy('lft')->select('services.*', 'favourites.id as _favid')
                            ->distinct()
                        // ->where('services.service_id',$request->service_id)
                            ->leftJoin('favourites','services.id','=','favourites.service_id')
                            ->where('services.status','=', 1)
                            ->orderBy('services.service_title', 'ASC')
                            ->get();
                }
              
            }
           
        }
       

       

        $resposne       =   [];
        $_servicesCat      = ServiceCategory::orderBy('lft')
                            ->where('parent_id','=', null)
                            ->get()->toArray();

        if( !empty($_servicesCat) ){
            foreach( $_servicesCat as $key => $data ){
                $resposne[$key]['id']                     =   $data['id'];
                $resposne[$key]['title']                  =   $data['cat_name'];
                $resposne[$key]['sub_service']            =   $this->getSubServiceCategory($data['id']);
            }
        }
        if( !empty($_servicesList) ){
            return response()->json([
                'status'                => true,
                'message'               => 'Success',
                'data'                  => $_servicesList,
                //'_category'             => $resposne,
            ],200);
        }else{
            return response()->json([
                'status'        => true,
                'message'       => 'No data',
                'data'          => $_servicesList,
                //'_category'     => $resposne,
            ],200);
        }
    }

    public function getServiceDetail( Request $request , $id ){

        $_servicesDetail     =   Service::orderBy('service_title', 'ASC')
                                    ->where('id','=', $id)
                                    ->get()->toArray();
       // print_r($_servicesDetail );
        if( !empty($_servicesDetail) ){
            return response()->json([
                'status'        => true,
                'message'       => 'Success',
                'data'          => $_servicesDetail,
            ],200);
        }else{
            return response()->json([
                'status'        => true,
                'message'       => 'No data',
                'data'          => $_servicesDetail,
            ],200);
        }
    }

    public function updateService( Request $request , $id = null ){

        $request->validate([

            'service_id'    =>  'required',
            'user_id'       =>  'required',
            'order_id'      =>  'required',
            'status'        =>  'required',
        ]);

        $_update =    ServiceRequest::updateOrInsert(
            ['id' => $request->order_id, 'customer_id' => $request->user_id , 'vendor_id' => $id , 'service_id' => $request->service_id ],
            [
                'status'     =>   $request->status,
                'updated_at' => Carbon::now()
            ]
        );

        if($_update){
            if( $request->status =='6' ){
                $_Orderupdate = Order::where('service_request_id',   $request->order_id)
                        ->update(['status' => 'complete','updated_at' => Carbon::now() ]);
            }
            
        
            return response()->json([
                        'status'        => true,
                        'message'       => 'Successfully service updated!',
                    ],200);
        }
        else{
            return response()->json([
                'status'        => false,
                'message'       => 'Error, check administrator!',
            ],200);
        }

    }
}
