<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\VendorLocation;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\Country;
use App\Models\VehicleTransmission;
use App\Models\VehicleClass;
use App\Models\Location;
use App\Models\CustomerVehicle;
use Illuminate\Support\Facades\Password;
use Mail; 
use Hash;
use DB; 

class UserController extends Controller
{
    //

    public function registerUser( Request $request ){

        $this->validate(
            $request, 
            [
                'name'          =>  'required|string',
                'email'         =>  'required|string|unique:users',
                'password'      =>  'required|string',
                //'c_password'    =>  'required|same:password',
                'mobile'        =>  'required|string',
                'location'      =>  'required',
                'user_type'     =>  'required'
            ],
            ['email.unique' => 'This email is already used for a different account']
        );
       

        $user = new User([
            'name'          =>  $request->name,
            'email'         =>  $request->email,
            'password'      =>  bcrypt($request->password),
            'mobile'        =>  $request->mobile,
            'location'      =>  $request->location,
            'latitude'      =>   ($request->has('latitude')) ? $request->latitude : null,
            'longitude'     =>   ($request->has('longitude')) ? $request->longitude : null
        ]);         
        $user->assignRole($request->user_type);

        if($user->save()){
            $tokenResult    = $user->createToken('apiToken');
            $token          = $tokenResult->plainTextToken;

            $_ID    =   \DB::getPdo()->lastInsertId();
            if ($request->filled(['label', 'building', 'area', 'block', 'road', 'house', 'latitude', 'longitude' ])) {
            $locations  = new Location(
                [
                    'label'         =>  $request->location_lable,
                    'building'      =>  $request->building,
                    'area'          =>  $request->location,
                    'block'         =>  $request->block,
                    'road'          =>  $request->road,
                    'house'         =>  $request->flat,
                    'user_id'       =>  $user->id,
                    'latitude'      =>   ($request->has('latitude')) ? $request->latitude : null,
                    'longitude'     =>   ($request->has('longitude')) ? $request->longitude : null,
                    'active'        =>  1,
                ]
            );
            $locations->save();
        }
 // Add condition to create a vehicle only if vehicle data is provided
 if ($request->filled(['veh_name', 'veh_country', 'veh_make', 'veh_model', 'veh_year', 'veh_class', 'veh_km', 'fuel_typ' ])) {
            $_vehicle   =    new CustomerVehicle([
                'label'                         =>  $request->veh_name,
                'vehicle_country'               =>  $request->veh_country,
                'vehicle_make'                  =>  $request->veh_make,
                'vehicle_model'                 =>  $request->veh_model,
                'vehicle_year'                  =>  $request->veh_year,
                'vehicle_transmission'          =>  $request->veh_trans,
                'vehicle_class'                 =>  $request->veh_class,
                'user_id'                       =>  $user->id,
                'km_run'                        =>  $request->veh_km,
                'fuel_type'                     =>  $request->fuel_type,
                'active'                        => 1
            ]);
            $_vehicle->save();

        }
            return response()->json([
                        'status'        => true,
                        'message'       => 'Successfully created user!',
                        'accessToken'   => $token,
                    ],201);
        }
        else{
            return response()->json(['error'=>'Provide proper details']);
        }
        //print_r($request->all());
    }

    public function updateUser( Request $request , $id ){

        $_update =    User::updateOrInsert(
            ['id' => $id],
            [
                'name'          =>  $request->name,
                'email'         =>  $request->email,
                //'password'      =>  bcrypt($request->password),
                'mobile'        =>  $request->mobile,
               // 'location'      =>  $request->location,
               // 'image'         => ( $request->filled('status') ) ?  $this->insertImage($request->image) : ,
               // 'latitude'      =>  $request->latitude,
               // 'longitude'     =>  $request->longitude,
                'status'        =>  ( $request->filled('status') ) ? $request->status : 1 ,
            ]
        );

        if($_update){
        
            return response()->json([
                        'status'        => true,
                        'message'       => 'Successfully user updated!',
                        'data'          => [
                            '_status'   => $request->status
                        ]
                    ],200);
        }
        else{
            return response()->json([
                'status'        => false,
                'message'       => 'Error, check administrator!',
            ],200);
        }
    }

    public function deactivateUser( Request $request , $id ){
        $_update =    User::updateOrInsert(
            ['id' => $id],
            [
               'deactivated' => 1
            ]
        );

        if($_update){
        
            return response()->json([
                        'status'        => true,
                        'message'       => 'User deleted successfully!',
                    ],200);
        }
        else{
            return response()->json([
                'status'        => false,
                'message'       => 'Error, check administrator!',
            ],200);
        }
    }

    public function updateFCMToken( Request $request , $id ){

        $request->validate([
            'fcm_token'    =>  'required',
        ]);

        $_update =    User::updateOrInsert(
            ['id' => $id],
            [
                'fcm_token'  =>  $request->fcm_token,
            ]
        );
        if($_update){
        
            return response()->json([
                        'status'        => true,
                        'message'       => 'Successfully token updated!',
            ],200);
        }
        else{
            return response()->json([
                'status'        => false,
                'message'       => 'Error, check administrator!',
            ],200);
        }
    }

    protected function insertImage( $value ){


    
        // or use your own disk, defined in config/filesystems.php
        $disk = config('backpack.base.root_disk_name');
        // destination path relative to the disk above
        $destination_path = "public/uploads/user";


        // if a base64 was sent, store it in the db
        if (Str::startsWith($value, 'data:image'))
        {
            // 0. Make the image
            $image = \Image::make($value)->encode('jpg', 90);

            // 1. Generate a filename.
            $filename = md5($value.time()).'.jpg';

            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path.'/'.$filename, $image->stream());

            $public_destination_path = Str::replaceFirst('public/', '', $destination_path);
            return  $public_destination_path.'/'.$filename;

        } elseif (!empty($value)) {
            // if value isn't empty, but it's not an image, assume it's the model value for that attribute.
            return $image;
        }
    }

    public function getRegFields( Request $request ){

        $response       =       [];

        $location               = VendorLocation::get()->toArray();
        $country                = Country::get()->toArray();
        $make                   = VehicleMake::get()->toArray();
        $model                  = VehicleModel::get()->toArray();
        $transmission           = VehicleTransmission::get()->toArray();
        $class                  = VehicleClass::get()->toArray();

        $vehicleSpecs           =   VehicleModel::select('vehicle_model.*')
                                        ->distinct()
                                        ->leftJoin('vehicle_classes','vehicle_model.v_class','=','vehicle_classes.id')
                                        ->leftJoin('vehicle_transmissions','vehicle_model.v_transmission','=','vehicle_transmissions.id')
                                        ->leftJoin('countries','vehicle_model.v_country','=','countries.id')
                                        ->get()
                                        ->toArray();

        //print_r($vehicleSpecs);


        
        if( !empty($transmission) ){
            foreach( $transmission as $_k => $data ){
                $response['transmission'][]  = [
                    'value' => $data['id'],
                    'label' => $data['name']
                ];
            }
        }

        if( !empty($class) ){
            foreach( $class as $_k => $data ){
                $response['class'][]  = [
                    'value' => $data['id'],
                    'label' => $data['name']
                ];
            }
        }

        if( !empty($location) ){
            foreach( $location as $_k => $data ){
                $response['location'][]  = [
                    'value' => $data['id'],
                    'label' => $data['name'],
                    
                ];
            }
        }
        if( !empty($location) ){
            foreach( $location as $_k => $data ){
                $response['vendor_lat_long'][]  = [
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    
                ];
            }
        }

        if( !empty($country) ){
            foreach( $country as $_k => $data ){
                $response['country'][]  = [
                    'value' => $data['id'],
                    'label' => $data['country_name']
                ];
            }
        }
        if( !empty($make) ){
            foreach( $make as $_k => $data ){
                $response['make'][]  = [
                    'value' => $data['id'],
                    'label' => $data['name']
                ];
            }
        }
        if( !empty($model) ){
            foreach( $model as $_k => $data ){
                $response['model']['make_'.$data['make']][]  = [
                    
                    'value' => $data['id'],
                    'label' => $data['name']
                ];
            }
        }

        if( !empty($vehicleSpecs) ){
            foreach( $vehicleSpecs as $_k => $data ){
                $response['vehiclespec']['model_'.$data['id']] = [
                    'v_class'               => $data['v_class'],
                    'v_transmission'        => $data['v_transmission'],
                    'v_country'             => $data['v_country'],
                ];
            }   
        }

        if($response){
        
            return response()->json([
                        'status'        => true,
                        'message'       => 'Success!',
                        'data'          => $response
                    ],200);
        }
        else{
            return response()->json([
                'status'        => true,
                'message'       => 'No data',
                'data'          => $response
            ],200);
        }
    }

    public function forgetPassword( Request $request ){

        $request->validate(['email' => 'required|email|exists:users']);
 
        $status = Password::sendResetLink(
            $request->only('email')
        );
        //echo $status;exit;
        return $status === Password::RESET_LINK_SENT
                    ? response()->json([
                        'status'        => true,
                        'message'       => 'Reset link sent successfully!!!',
                        'data'          => $status
                    ],200)
                    : response()->json([
                        'status'        => false,
                        'message'       => 'Somethigng went wrong, check administrator '. $status,
                        'data'          => $status
                    ],200);

    }

    public function resetPassword( Request $request ){

    }

  
}
