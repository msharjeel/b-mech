<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\UsersMeta;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Api;
use App\Models\ServiceCategory;
use App\Models\VendorLocation;
use App\Models\Service;
use App\Models\Notification;
use Laravel\Sanctum\PersonalAccessToken;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});




Route::post('/create/token', function (Request $request) {

    $request->validate([
        'email'             => 'required|email',
        'password'          => 'required',
        'device_name'       => 'required',
    ]);
 
   // $referer               =   $request->referer;

    $user = User::where('email', $request->email)->whereHas('roles', function($q) use ($request){
                    $q->where('name',  $request->role);
                    $q->where('deactivated',  0);
            })->first();
           // print_r($user);exit;
    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $_notification = Notification::whereJsonContains('users',"$user->id")
                        ->get()->toArray();

                        //print_r($_notification);exit;    

    $_servicesCat      = ServiceCategory::orderBy('cat_name', 'ASC')
                            ->where('parent_id','=', null)
                            ->get()->toArray();

    if( !empty($_servicesCat) ){
        foreach( $_servicesCat as $_key => $_data ){
            $resposne[$_key]['id']                     =   $_data['id'];
            $resposne[$_key]['title']                  =   $_data['cat_name'];
            $resposne[$_key]['image']                  =   $_data['image'];
        }
    }
    
    $serviceOffered         = UsersMeta::where('user_id',$user->id)
                                ->pluck('service_id')->toArray();
    $_serviceOffered        = [];
    
    if( !empty($serviceOffered ) ){
        foreach( $serviceOffered  as $_k => $_d ){
            $_serviceOffered[]      =    Service::getCatID($_d);
        }
    }
   // print_r( array_unique($_serviceOffered) );

    $location               = VendorLocation::get()->toArray();

    if( !empty($location) ){
        foreach( $location as $_k => $data ){
            $response['location'][]  = [
                'value' => $data['id'],
                'label' => $data['name']
            ];
        }
    }
    if( !empty($user->vendor_location_range) ){
        $_vLocation = VendorLocation::getVendorLocation($user->vendor_location_range);
    }else{
        $_vLocation = [];
    }
   
  //  $notification   =   Notification::where()-
    return response()->json([
        'status'                => true,
        'message'               => 'Successfuly created token',
        'token'                 => 'Bearer',
        'data'                  => $user->createToken($request->device_name)->plainTextToken,
        'user_id'               => $user->id,
        'user_email'            => $user->email,
        'user_name'             => $user->name,
        '_status'               => $user->status,
        'category'              => $resposne,
        'service_offered'       => array_unique($_serviceOffered),
        'location'              => $location,
        'location_range'        => $_vLocation,
        '_notifications'        => ( !empty($_notification) ) ? true : false,
        'referer'               => (  $request->referer !=null )  ? $request->referer : null 
    ],200);
  

});



//Admin Api to fetch data 

Route::post('/api/model', 'App\Http\Controllers\Api\VehicleModelController@index');


//Mobile Api
Route::post('/register', 'App\Http\Controllers\Api\UserController@registerUser');

Route::get('/regfields', 'App\Http\Controllers\Api\UserController@getRegFields');
Route::post('/forgot-password', 'App\Http\Controllers\Api\UserController@forgetPassword');

Route::post('/payment-success/{id}/{user}', 'App\Http\Controllers\Api\TransactionController@paymentSuccess')->name('payment-success');
Route::post('/payment-error/{id}/{reqid}', 'App\Http\Controllers\Api\TransactionController@paymentError')->name('payment-error');


Route::get('/dashboard', 'App\Http\Controllers\Api\DashboardController@index');

Route::group([
    'prefix'     => 'guest',
    'namespace'  => 'App\Http\Controllers\Api',
], function () {
    
    Route::get('/dashboard', 'DashboardController@index');
    Route::get('/services', 'ServiceController@getService');
    Route::post('/service-detail/{id}', 'ServiceController@getServiceDetail');
    Route::get('/home-vendors', 'VendorController@getHomeVendor');
});
//User Api
Route::group([
    'prefix'     => 'user',
    'middleware' => 'auth:sanctum',
    'namespace'  => 'App\Http\Controllers\Api',
], function () { 

    Route::get('/users', function (Request $request) {
        return $request->user();
    });
    Route::post('/update-user/{id}','UserController@updateUser');

    Route::post('/update-token/{id}','UserController@updateFCMToken');

    Route::get('/service-categories', 'ServiceController@getServiceCategories');
    Route::get('/services', 'ServiceController@getService');
    Route::post('/review/{id}', 'ReviewController@postReview');
    Route::post('/service-detail/{id}', 'ServiceController@getServiceDetail');
    Route::post('/service-vendors/{id}', 'VendorController@getAllVendors');
    Route::post('/add-favourite/{id}', 'FavouriteController@addFavourite');
    Route::get('/list-favourites/{id}', 'FavouriteController@listFavourite');
    //Route::get('/list-favourites', 'FavouriteController@listFavourite');
    Route::get('/get-address', 'LocationController@getAddress');
    Route::post('/add-address/{id}', 'LocationController@addAddress');
    Route::post('/update-address/{id}', 'LocationController@updateAddress');
    Route::get('/get-vehicles', 'VehicleController@getVehicle');
    Route::post('/add-vehicle/{id}', 'VehicleController@addVehicle');
    Route::post('/update-vehicle/{id}', 'VehicleController@updateVehicle');
    Route::get('/orders/{id}', 'OrderController@listOrders');
    Route::get('/order-detail/{id}', 'OrderController@detailedOrder');
    Route::post('/service-request/{id}', 'ServiceRequestController@requestService');
    Route::get('/filter', 'FilterController@filterResult');
    Route::get('/dashboard', 'DashboardController@index');

    Route::post('/delete-address/{id}', 'LocationController@deleteAddress');
    Route::post('/delete-vehicle/{id}', 'VehicleController@deleteVehicle');
    Route::post('/help-request/{id}', 'HelpController@_helpRequest');

    Route::get('/home-vendors', 'VendorController@getHomeVendor');

    Route::post('/validate-coupon/{id}', 'CouponController@validateCoupon');

    Route::get('/get-notifications/{id}', 'NotificationController@indexUser');
    Route::post('/delete-notifications/{id}', 'NotificationController@deleteNotifications');

    Route::post('/user-vendor-request/{id}', 'VendorController@userVendorRequest');
    Route::post('/service-cancel/{id}', 'VendorController@cancelService');

    Route::post('/deactivate-user/{id}','UserController@deactivateUser');
    
   
});


//Vendor Api
Route::group([
    'prefix'     => 'vendor',
    'middleware' => 'auth:sanctum',
    'namespace'  => 'App\Http\Controllers\Api',
], function () { 

    Route::post('/update-request', 'ServiceRequestController@updateRequestStatus');
    Route::post('/update-token/{id}','UserController@updateFCMToken');
    Route::get('/service-vendors/{id}', 'VendorController@getUserReqDetails');
    Route::post('/service-accept-reject/{id}', 'VendorController@acceptReject');
    Route::get('/get-vendor-orders/{id}', 'OrderController@getVendorOrder');
    Route::post('/help-request/{id}', 'HelpController@_helpRequest');
    Route::get('/dashboard', 'DashboardController@getVendorDashboard');
    Route::post('/update-user/{id}','UserController@updateUser');
    Route::post('/update-service/{id}','ServiceController@updateService');
    Route::get('/get-notifications/{id}', 'NotificationController@indexVendor');
    Route::post('/delete-notifications/{id}', 'NotificationController@deleteNotifications');
    Route::get('/reports/{id}', 'ReportsController@index');
    Route::get('/generate-report/{id}', 'ReportsController@generateReport');
   // Route::post('/forgot-password', 'UserController@forgetPassword');
});