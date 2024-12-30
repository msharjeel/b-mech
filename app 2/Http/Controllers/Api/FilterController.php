<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Service;

class FilterController extends Controller
{
    //

    public function filterResult( Request $request ){

        $query              =    $request->q;
        $_request           =    $request->r;

        $_filterResult  =   [];
        if( $_request == 'vendor' ){
            $_filterResult     = User::where('name', 'LIKE', '%'.$query.'%')
                                    ->where('status','=',1)
                                    ->whereHas(
                                        'roles', function($q){
                                            $q->where('name', 'Vendor');
                                        }
                                    )->orderBy('name', 'ASC')
                                    ->get()->toArray();

        }else if( $_request == 'service' ){

                $_filterResult     = Service::where('service_title', 'LIKE', '%'.$query.'%')
                                        ->orderBy('service_title', 'ASC')
                                        ->get()->toArray();
        }
        


        
        if( !empty($_filterResult) ){
            return response()->json([
                'status'        => true,
                'message'       => 'Success',
                'data'          => $_filterResult,
            ],200);
        }else{
            return response()->json([
                'status'        => true,
                'message'       => 'No data',
                'data'          => $_filterResult,
            ],200);
        }

    }

}
