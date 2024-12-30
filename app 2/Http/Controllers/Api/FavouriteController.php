<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favourites;
use App\Models\Service;
class FavouriteController extends Controller
{
    //

    public function addFavourite( Request $request , $id ){

        $_serviceId         =  $request->service_id;
        $request->validate([
            //'user_id'          =>  'required',
            'service_id'       =>  'required',
            
        ]);

        if( $request->action =='add'){
            $_fav  =   new Favourites([
                'user_id'       => $id,
                'service_id'    => $request->service_id
            ]);

            if($_fav->save()){
                return response()->json([
                            'status'        => true,
                            'message'       => 'Successfully added!',
                        ],200);
                }
            else{
                return response()->json([
                    'status'        => false,
                    'message'       => 'Error',
                ],200);
            }
        }

        if( $request->action =='remove'){

            $delete  = Favourites::where('user_id', $id)
                        ->where('service_id',$request->service_id)
                        ->delete();
            if($delete){
                return response()->json([
                            'status'        => true,
                            'message'       => 'Deleted successfully!',
                        ],200);
                }
            else{
                return response()->json([
                    'status'        => false,
                    'message'       => 'Error',
                ],200);
            }
        }
        
    }

    public function listFavourite( Request $request , $id ){

        $_userId            =   $id;

        // $request->validate([
        //     'user_id'          =>  'required',
        // ]);

        $_favList           = Service::select('services.*', 'favourites.id as _favid')
                                ->distinct()
                                ->leftJoin('favourites','services.id','=','favourites.service_id')
                                ->where('favourites.user_id',$_userId)
                                ->orderBy('services.service_title', 'ASC')
                                ->get();

        if( !empty($_favList) ){
            return response()->json([
                'status'        => true,
                'message'       => 'Success',
                'data'          => $_favList,
            ],200);
        }else{
            return response()->json([
                'status'        => true,
                'message'       => 'No data',
                'data'          => $_favList,
            ],200);
        }
    }
}
