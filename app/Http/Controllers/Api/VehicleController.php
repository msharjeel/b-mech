<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\VehicleCylinder;
use App\Models\VehicleClass;
use App\Models\VehicleDisplacement;
use App\Models\VehicleTransmission;
use App\Models\VehicleDrive;
use App\Models\CustomerVehicle;

class VehicleController extends Controller
{
    //

    public function getVehicle( Request $request ){

        $_userId        =   $request->user_id;
        
        $request->validate([
            'user_id'     =>  'required',
        ]);

        $_vehicle       =  CustomerVehicle::where('user_id', '=', $_userId )
                             ->where('deleted_at',null)
                             ->orderBy("active", 'desc')
                             ->get()->toArray();

        if( !empty($_vehicle) ){

                return response()->json([
                    'status'        => true,
                    'message'       => 'Success',
                    'data'          => $_vehicle,
                ],
            200);
            }else{
                return response()->json([
                    'status'        => true,
                    'message'       => 'No data',
                    'data'          => $_vehicle,
                ],
            200);
        }
    }

    public function addVehicle( Request $request ,$id ){

        $request->validate([
            'label'                     =>  'required',
            'vehicle_make'              =>  'required',
            'vehicle_model'             =>  'required',
            'vehicle_year'              =>  'required',
            'vehicle_transmission'      =>  'required',
            //'vehicle_drive'             =>  'required',
            //'vehicle_displacement'      =>  'required',
            //'vehicle_cylinder'          =>  'required',
            'vehicle_class'             =>  'required',
            //'user_id'                   =>  'required',
        ]);

        $existVehicle   =   CustomerVehicle::where('user_id', $id)->get()->toArray();

        $_vehicle      = new CustomerVehicle([
            'label'                         =>   $request->label,
            'vehicle_make'                  =>   $request->vehicle_make,
            'vehicle_model'                 =>   $request->vehicle_model,
            'vehicle_year'                  =>   $request->vehicle_year,
            'vehicle_transmission'          =>   $request->vehicle_transmission,
            'vehicle_drive'                 =>   $request->vehicle_drive,
            'vehicle_displacement'          =>   $request->vehicle_displacement,
            'vehicle_cylinder'              =>   $request->vehicle_cylinder,
            'vehicle_class'                 =>   $request->vehicle_class,
            'km_run'                        =>   $request->km,
            'fuel_type'                     =>   $request->fuel_type,
            'vehicle_country'               =>   $request->country,
            'user_id'                       =>   $id,
            'active'                        =>   ( empty($existVehicle)  ) ? 1 : 0
        ]);

        if($_vehicle->save()){
        
            return response()->json([
                        'status'        => true,
                        'message'       => 'Successfully vehicle added!',
                    ],200);
        }
        else{
            return response()->json([
                'status'        => false,
                'message'       => 'Error, check administrator!',
            ],200);
        }

    }

    public function updateVehicle( Request $request , $id ){

        // $request->validate([
        //     'user_id'   =>  'required',
        // ]);

        if( $request->status =='active' ){
             CustomerVehicle::where('active', 1)->where('user_id',$id)->update(['active' => 0]);
        }

        $_update =    CustomerVehicle::updateOrInsert(
            ['id' => $request->_id, 'user_id' => $id ],
            [
                'label'                         =>   $request->label,
                'vehicle_make'                  =>   $request->vehicle_make,
                'vehicle_model'                 =>   $request->vehicle_model,
                'vehicle_year'                  =>   $request->vehicle_year,
                'vehicle_transmission'          =>   $request->vehicle_transmission,
                'vehicle_drive'                 =>   $request->vehicle_drive,
                'vehicle_displacement'          =>   $request->vehicle_displacement,
                'vehicle_cylinder'              =>   $request->vehicle_cylinder,
                'vehicle_class'                 =>   $request->vehicle_class,
                'km_run'                        =>   $request->km,
                'fuel_type'                     =>   $request->fuel_type,
                'vehicle_country'               =>   $request->country,
                'active'                        =>   $request->active,
            ]
        );
        if($_update){
        
            return response()->json([
                        'status'        => true,
                        'message'       => 'Successfully vehicle updated!',
                    ],200);
        }
        else{
            return response()->json([
                'status'        => false,
                'message'       => 'Error, check administrator!',
            ],200);
        }

    }

    public function getCountry( Request $request ){

        $country = Country::get()->toArray();

        if($country){
        
            return response()->json([
                        'status'        => true,
                        'message'       => 'Success',
                        'data'          => $country 
                    ],200);
        }
        else{
            return response()->json([
                'status'        => true,
                'message'       => 'No data!',
            ],200);
        }
    }

    private function getMake(  $id ){

        $make   =   VehicleMake::where('country_id',  $id)->get()->toArray();
        if($make){
        
            return $make;
        }
    }

    private function getModel( $id){

        $model   =   VehicleModel::where('make',  $id)->get()->toArray();
        if($model){
            return $model;
        }
        
    }

    public function getDrive( Request $request ){

        $drive   =   VehicleDrive::get()->toArray();
        if($drive){
        
            return response()->json([
                        'status'        => true,
                        'message'       => 'Success',
                        'data'          => $drive 
                    ],200);
        }
        else{
            return response()->json([
                'status'        => true,
                'message'       => 'No data!',
            ],200);
        }
    }

    public function getTransmission( Request $request ){

        $transmission   =   VehicleTransmission::get()->toArray();
        if($transmission){
        
            return response()->json([
                        'status'        => true,
                        'message'       => 'Success',
                        'data'          => $transmission 
                    ],200);
        }
        else{
            return response()->json([
                'status'        => true,
                'message'       => 'No data!',
            ],200);
        }
    }

    public function getDisplacement( Request $request ){

    }

    public function getCylinders( Request $request ){

    }

    public function getClasses( Request $request ){

    }

    public function deleteVehicle( Request $request , $id ){

        $request->validate([
            'delete_id'     =>  'required',
        ]);
        $ids                =   $request->delete_id;
        //echo  $ids;

        $delete  = CustomerVehicle::whereIn('id', explode(',',$ids))->delete();

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
