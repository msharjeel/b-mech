<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\DeletedNotifications;
use Carbon\Carbon;

class NotificationController extends Controller
{
    //

    public function indexUser( Request $request , $id = null ){

        $deletedNotification     = [];
        $deletedNotification     = DeletedNotifications::where('user_id',$id)
                                            ->select('notification_id')
                                            //where('user_id',$id)
                                            ->get()
                                            ->toArray();
        
        $deletedNotification = collect($deletedNotification);
        $plucked = $deletedNotification->pluck('notification_id');
        $deletedNotification = $plucked->all();
       // print_r($deletedNotification);


       $notify = Notification::whereJsonContains('users',"$id")
                        ->orderBy('id', 'DESC')
                        ->get()->toArray();
     
        $filter     =   [];
        if( !empty($notify ) ){
            foreach( $notify  as $_k => $_v ){
                if( !in_array($_v['id'], $deletedNotification) ){
                    $filter[$_k]    =   $_v;
                }
            }
        }
        //print_r($filter );
                

        if( !empty($notify) ){
            return response()->json([
                'status'        => true,
                'message'       => 'Success',
                'data'          => array_values($filter),
            ],200);
        }else{
            return response()->json([
                'status'        => true,
                'message'       => 'No data',
                'data'          => array_values($filter),
            ],200);
        }

    }

    public function indexVendor( Request $request , $id = null ){
        $deletedNotification     = [];

        $deletedNotification     = DeletedNotifications::where('user_id',$id)
                                            ->select('notification_id')
                                            ->get()
                                            ->toArray();

        $deletedNotification = collect($deletedNotification);
        $plucked = $deletedNotification->pluck('notification_id');
        $deletedNotification = $plucked->all();

        $notify = Notification::whereJsonContains('vendors',"$id")
                            ->orderBy('id', 'DESC')
                            ->get()->toArray();

      
        $filter     =   [];
        if( !empty($notify ) ){
            foreach( $notify  as $_k => $_v ){
                if( !in_array($_v['id'], $deletedNotification) ){
                    $filter[$_k]    =   $_v;
                }
            }
        }

        if( !empty($notify) ){
            return response()->json([
                'status'        => true,
                'message'       => 'Success',
                'data'          => array_values($filter),
            ],200);
        }else{
            return response()->json([
                'status'        => true,
                'message'       => 'No data',
                'data'          => array_values($filter),
            ],200);
        }

    }

    public function deleteNotifications( Request $request , $id = null ){

        $request->validate([
            'notification_id'   =>  'required',
        ]);
        
        $notiifcationIds    = explode( ',',$request->notification_id );
        $result             =   false;
       // print_r($notiifcationIds);
        if( !empty($notiifcationIds) ){
            foreach( $notiifcationIds as $_k => $_d ){
                
                DeletedNotifications::insert(
                    [
                    'notification_id'   => $_d,
                    'user_id'           => $id,
                    'created_at'        => Carbon::now(),
                    'updated_at'        => Carbon::now(),
                    ]
                );
                $result             =   true;
            }
        }

        if(  $result ){
            return response()->json([
                'status'        => true,
                'message'       => 'Success',
                'data'          => [],
            ],200);
        }


    }
}
