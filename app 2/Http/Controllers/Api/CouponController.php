<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Order;

class CouponController extends Controller
{
    //

    public function validateCoupon( Request $request , $id = null ){

        $request->validate([
            'order_amount'          =>  'required', 
            'coupon_code'           =>  'required', 
            'vendor_id'             =>  'required'
        ]);

        $coupon = Coupon::where('name', $request->coupon_code )
                    ->get()
                    ->toArray();

        $coupon     =   ( !empty($coupon) ) ? $coupon[0] : [];
     
        if( empty($coupon) ) {

            return response()->json([
                'status'        => false,
                'message'       => 'Invalid coupon',
                'data'          => []
            ],200);
        }
   
        $expiry     =    new \DateTime($coupon['expiry']);
        
        if( new \DateTime(date('d-m-Y')) > $expiry ){

            return response()->json([
                'status'        => false,
                'message'       => 'Coupon expired',
                'data'          => []
            ],200);
        }
 
       // print_r($coupon);
        if( !empty($coupon['min_spend']) && $request->order_amount < $coupon['min_spend'] ){
            return response()->json([
                'status'        => false,
                'message'       => 'Minimum order amount should be '.$coupon['min_spend'],
                'data'          => []
            ],200);
        }
    
        if( !empty($coupon['max_spend']) && $request->order_amount < $coupon['max_spend'] ){
            return response()->json([
                'status'        => false,
                'message'       => 'Maximum order amount should be '.$coupon['max_spend'],
                'data'          => []
            ],200);
        }

        if( !empty($coupon['exculde_vendor']) && in_array( $request->vendor_id, $coupon['exculde_vendor']) ){
            return response()->json([
                'status'        => false,
                'message'       => 'Coupon not valid for this vendor',
                'data'          => []
            ],200);
        }

        if( !empty($coupon['exculde_user']) && in_array( $id , $coupon['exculde_user']) ){
            return response()->json([
                'status'        => false,
                'message'       => 'Coupon is not invalid for you',
                'data'          => []
            ],200);
        }

        $orderCoupon    = Order::where('coupon_id', $coupon['id'])
                            ->select('id')
                            ->get()
                            ->toArray();
        $orderCoupon    =   ( !empty($orderCoupon) ) ? count($orderCoupon) : '';
        if( !empty($orderCoupon) && $orderCoupon >= $coupon['usage_limit'] ){
            return response()->json([
                'status'        => false,
                'message'       => 'Coupon usage exceeded',
                'data'          => []
            ],200);
        }
        $discoutPrice           =   0;

        if( $coupon['type'] =='fixed' ){
            $discoutPrice = ( $request->order_amount - $coupon['amount'] );
        }

        if( $coupon['type'] =='percentage' ){
            $discoutPrice =  $request->order_amount - ( $request->order_amount * $coupon['amount'] / 100  );
        }

        $vat                                                     =   round(( $discoutPrice / 100 ) * 10 , 2);
       
        return response()->json([
            'status'        => true,
            'message'       => 'Successfully applied',
            'data'          => [
                '_orderTotal'           => $discoutPrice,
                '_discountPrice'        => $coupon['amount'],
                '_vat'                  => $vat,
                '_total_after_vat'      => $discoutPrice + $vat,
                '_discount_type'        => $coupon['type'],
                '_coupon_id'            => $coupon['id'],
                '_coupon_name'          => $coupon['name']
            ]
        ],200);


    }
}
