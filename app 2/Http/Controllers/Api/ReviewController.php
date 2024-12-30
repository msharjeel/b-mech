<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Order;
class ReviewController extends Controller
{
    //
    public function postReview( Request $request , $id = null ){

        $request->validate([
            //'user_id'          =>  'required',
            'vendor_id'        =>  'required',
            'service_id'       =>  'required',
            'order_id'         =>  'required',
            //'rating'           =>  'required',
        ]);

        $_update =    Order::updateOrInsert(
            ['id' => $request->order_id, 'user_id' => $id ],
            [
                'order_rated' => 1
            ]
        );

        $_review = new Review([
            'user_id'           =>  $id,
            'vendor_id'         =>  $request->vendor_id,
            'seervice_id'        =>  $request->service_id,
            'order_id'          =>  $request->order_id,
            'rating'            =>  ( $request->has('rating') ) ?  $request->rating : 0,
            'review'            =>  $request->comment
        ]);

        if($_review->save()){
            return response()->json([
                        'status'        => true,
                        'message'       => 'Successfully updated review',
                        
                    ],200);
        }
        else{
            return response()->json([
                'status'        => false,
                'message'       => 'Error, please check administrator',
                
            ],200);
        }

    }
}
