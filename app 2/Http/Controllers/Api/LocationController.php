<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\User;

class LocationController extends Controller
{
    //

    public function addAddress( Request $request , $id ){

        //print_r( $request->all());

        $request->validate([
            'label'     =>  'required',
            //'city'      =>  'required',
            'area'      =>  'required',
            'block'     =>  'required',
            'road'      =>  'required',
            'house'     =>  'required',
           // 'user_id'   =>  'required',
        ]);

        $existLocation   =   Location::where('user_id', $id)->get()->toArray();

        $_location      = new Location([
            'label'         =>   $request->label,
            'building'      =>   $request->building,
            'area'          =>   $request->area,
            'block'         =>   $request->block,
            'road'          =>   $request->road,
            'house'         =>   $request->house,
            'user_id'       =>   $id,
            'active'        =>   ( empty($existLocation) ) ? 1 : 0,
            'latitude'      =>   ($request->has('latitude')) ? $request->latitude : 26.0667,
            'longitude'     =>   ($request->has('longitude')) ? $request->longitude : 50.5577,
        ]);

        if($_location->save()){

            User::updateOrInsert(
                ['id' => $id ],
                [
                    'latitude'      =>   ($request->has('latitude')) ? $request->latitude : 26.0667,
                    'longitude'     =>   ($request->has('longitude')) ? $request->longitude : 50.5577,
                ]
            );
        
            return response()->json([
                        'status'        => true,
                        'message'       => 'Successfully location added!',
                    ],200);
        }
        else{
            return response()->json([
                'status'        => false,
                'message'       => 'Error, check administrator!',
            ],200);
        }
    }

    public function getAddress( Request $request ){

        $_userId        =   $request->user_id;
        
        $request->validate([
            'user_id'     =>  'required',
        ]);
        
        $_address       =  Location::select('locations.*', 'vendor_locations.name as _area')
                                ->distinct()
                                ->where('user_id', '=', $request->user_id )
                                ->rightJoin('vendor_locations','locations.area','=','vendor_locations.id')
                                ->where('locations.deleted_at',null)
                                ->orderBy("locations.active", 'desc')
                                ->get()->toArray();
        if( !empty($_address) ){
            return response()->json([
                'status'        => true,
                'message'       => 'Success',
                'data'          => $_address,
            ],200);
        }else{
            return response()->json([
                'status'        => true,
                'message'       => 'No data',
                'data'          => $_address,
            ],200);
        }
    }

    public function updateAddress( Request $request  , $id ){

        $request->validate([
            '_id'   =>  'required',
        ]);
    
        if( $request->status =='active' ){
            Location::where('active', 1)->where('user_id', $id)->update(['active' => 0]);
        }
   
        
        $_update =    Location::updateOrInsert(
            ['id' => $request->_id, 'user_id' => $id ],
            [
                'label'     =>   $request->label,
                'building'  =>   $request->building,
                'area'      =>   $request->area,
                'block'     =>   $request->block,
                'road'      =>   $request->road,
                'house'     =>   $request->house,
                'active'    =>   $request->active,
                'latitude'      =>   ($request->has('latitude')) ? $request->latitude : 26.0667,
                'longitude'     =>   ($request->has('longitude')) ? $request->longitude : 50.5577,
            ]
        );
        if($_update){
        
            return response()->json([
                        'status'        => true,
                        'message'       => 'Successfully location updated!',
                    ],200);
        }
        else{
            return response()->json([
                'status'        => false,
                'message'       => 'Error, check administrator!',
            ],200);
        }
    }

    public function deleteAddress( Request $request , $id ){

        $request->validate([
            'delete_id'     =>  'required',
        ]);
        $ids                =   $request->delete_id;
        //echo  $ids;

        $delete  = Location::whereIn('id', explode(',',$ids))->delete();

        if($delete){
        
            return response()->json([
                        'status'        => true,
                        'message'       => 'Successfully deleted!',
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
