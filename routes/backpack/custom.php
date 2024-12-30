<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
 
    
    Route::crud('service', 'ServiceCrudController');
    Route::crud('service-category', 'ServiceCategoryCrudController');
    Route::crud('review', 'ReviewCrudController');
    Route::crud('service-request', 'ServiceRequestCrudController');
    Route::crud('vendor', 'VendorCrudController');
    Route::crud('location', 'LocationCrudController');
    Route::crud('customer-vehicle', 'CustomerVehicleCrudController');
    Route::crud('vehicle-make', 'VehicleMakeCrudController');
    Route::crud('vehicle-model', 'VehicleModelCrudController');
    //Route::post('/api/category', 'Api\CategoryController@index');
    Route::crud('vehicle-transmission', 'VehicleTransmissionCrudController');
    Route::crud('vehicle-cylinder', 'VehicleCylinderCrudController');
    Route::crud('vehicle-class', 'VehicleClassCrudController');
    Route::crud('vehicle-drive', 'VehicleDriveCrudController');
    Route::crud('vehicle-displacement', 'VehicleDisplacementCrudController');
    Route::crud('order', 'OrderCrudController');
    Route::crud('country', 'CountryCrudController');
    Route::crud('vendor-location', 'VendorLocationCrudController');
    Route::crud('banner', 'BannerCrudController');
    Route::crud('help', 'HelpCrudController');
    Route::crud('coupon', 'CouponCrudController');
    Route::crud('notification', 'NotificationCrudController');
    Route::crud('transaction', 'TransactionCrudController');
    Route::crud('reports', 'ReportsCrudController');
    Route::crud('user-request-log', 'UserRequestLogCrudController');
}); // this should be the absolute last line of this file