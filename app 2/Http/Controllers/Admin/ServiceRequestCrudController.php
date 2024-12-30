<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ServiceRequestRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\User;
use App\Models\ServiceRequest;
use App\Models\Service;
use App\Models\CustomerVehicle;
use App\Models\Location;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\VehicleTransmission;
use App\Models\VehicleDrive;
use App\Models\VehicleDisplacement;
use App\Models\VehicleClass;
use App\Models\VehicleCylinder;
use App\Models\ServiceCategory;
use App\Models\Order;

/**
 * Class ServiceRequestCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ServiceRequestCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\ServiceRequest::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/service-request');
        CRUD::setEntityNameStrings('service request', 'service requests');
        CRUD::enableDetailsRow();
       
    }

    protected function setupShowOperation()
    {
        $this->setupListOperation();
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->removeButton('create');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
        CRUD::addColumn([
            'name'          => 'customer_id',
            'label'         => 'Order ID', 
            'type'          => 'model_function',
            'function_name' => 'getOrderID', 
        ]); 
         
         CRUD::addColumn([
            'name'          => 'customer_id',
            'label'         => 'Customer Name', 
            'type'          => 'model_function',
            'function_name' => 'getCustomerName', 
        ]); 

        CRUD::addColumn([
            'name'          => 'vendor_id',
            'label'         => 'Vendor Name', 
            'type'          => 'model_function',
            'function_name' => 'getVendorName', 
        ]); 

        CRUD::addColumn([
            'name'          => 'vehicle_id',
            'label'         => 'Vehicle name', 
            'type'          => 'model_function',
            'function_name' => 'getVehicleName', 
        ]); 

        CRUD::addColumn([
            'name'          => 'service_id',
            'label'         => 'Service name', 
            'type'          => 'model_function',
            'function_name' => 'getServiceName', 
        ]); 

        CRUD::addColumn([
            'name'          => 'vehicle_id',
            'label'         => 'Vehicle make', 
            'type'          => 'model_function',
            'function_name' => 'getVehicleName', 
        ]); 

        CRUD::addColumn([
            'name'          => 'location_id',
            'label'         => 'Location', 
            'type'          => 'model_function',
            'function_name' => 'getLocationName', 
        ]); 
        CRUD::addColumn([
            'name'          => 'status',
            'label'         => 'Status', 
            'type'          => 'model_function',
            'function_name' => 'getStatus', 
        ]); 
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ServiceRequestRequest::class);

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */

         CRUD::addField([  // Select
            'label'     => "Services",
            'type'      => 'select',
            'name'      => 'service_id', // the db column for the foreign key
         
            // optional - manually specify the related model and attribute
            'model'     => "App\Models\Service", // related model
            'attribute' => 'service_title', // foreign key attribute that is shown to user
         
            // optional - force the related options to be a custom query, instead of all();
            'options'   => (function ($query) {
                 return $query->orderBy('service_title', 'ASC')->get();
             }), //  you can use this to filter the results show in the select
         ]);

         CRUD::addField([  // Select
            'label'     => "User",
            'type'      => 'select',
            'name'      => 'customer_id', // the db column for the foreign key
         
            // optional - manually specify the related model and attribute
            'model'     => "App\Models\User", // related model
            'attribute' => 'name', // foreign key attribute that is shown to user
         
            // optional - force the related options to be a custom query, instead of all();
            'options'   => (function ($query) {
                return User::whereHas(
                            'roles', function($q){
                                $q->where('name', 'Customer');
                            }
                    )->get();
             }), //  you can use this to filter the results show in the select
         ]);

         CRUD::addField([  // Select
            'label'     => "Vendor",
            'type'      => 'select',
            'name'      => 'vendor_id', // the db column for the foreign key
         
            // optional - manually specify the related model and attribute
            'model'     => "App\Models\User", // related model
            'attribute' => 'name', // foreign key attribute that is shown to user
         
            // optional - force the related options to be a custom query, instead of all();
            'options'   => (function ($query) {
                 return User::whereHas(
                        'roles', function($q){
                            $q->where('name', 'Vendor');
                        }
                    )->get();
             }), //  you can use this to filter the results show in the select
         ]);

         CRUD::addField([  // Select
            'label'     => "User vehicle",
            'type'      => 'select',
            'name'      => 'vehicle_id', // the db column for the foreign key
         
            // optional - manually specify the related model and attribute
            'model'     => "App\Models\CustomerVehicle", // related model
            'attribute' => 'label', // foreign key attribute that is shown to user
         
            // optional - force the related options to be a custom query, instead of all();
            'options'   => (function ($query) {
                 return $query->orderBy('label', 'ASC')->get();
             }), //  you can use this to filter the results show in the select
         ]);

         CRUD::addField([  // Select
            'label'     => "User location",
            'type'      => 'select',
            'name'      => 'location_id', // the db column for the foreign key
         
            // optional - manually specify the related model and attribute
            'model'     => "App\Models\Location", // related model
            'attribute' => 'label', // foreign key attribute that is shown to user
         
            // optional - force the related options to be a custom query, instead of all();
            'options'   => (function ($query) {
                 return $query->orderBy('label', 'ASC')->get();
             }), //  you can use this to filter the results show in the select
         ]);

         CRUD::addField([   // select_from_array
            'name'        => 'status',
            'label'       => "Status",
            'type'        => 'select_from_array',
            'options'     => ['0' => 'On the way', '1' => 'Cancelled','2' => 'Ready to pickup your car','3' => 'At Workshop' , '4' => 'Working on it' , '5' => 'On the way back', '6' => 'All done'],
            'allows_null' => false,
            'default'     => '1',
            // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;
        ]);
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function showDetailsRow( $id ){

        $_vehicleId         = ServiceRequest::where('id',$id)->pluck('vehicle_id')[0];
        $_locationId        = ServiceRequest::where('id',$id)->pluck('location_id')[0];
        $_ServiceId         = ServiceRequest::where('id',$id)->pluck('service_id')[0];
        $_CustomerId        = ServiceRequest::where('id',$id)->pluck('customer_id')[0];
        $_VendorId          = ServiceRequest::where('id',$id)->pluck('vendor_id')[0];

        $_serviceDetails    = Service::where('id',$_ServiceId)->get()[0];
        $_vehicleDetails    = CustomerVehicle::where('id',$_vehicleId)->get()->toArray()[0];
        $_locationDetails   = Location::where('id',$_locationId)->get()->toArray()[0];

        $userDetails        =   User::where('id', $_CustomerId)->get()->toArray()[0];
        $vendorDetails      =   User::where('id', $_VendorId)->get()->toArray()[0];

        $_order             =   Order::where('service_request_id',$id)->get()->toArray()[0];

       // print_r($_serviceDetails->category_id);//exit;
        $html               = '<table>
                                <tr>
                                <th>Service details</th>
                                <th>Vehicle details</th>
                                <th>Location details</th>
                                <th>Customer Details</th>
                                <th>Vendor Details</th>
                                </tr>
                                <tr>
                                    <td><b>Order ID #'.$_order['id'].'</b><br/>
                                        Service Title : '. '<b>'.$_serviceDetails->service_title.'</b>'.'<br/>Service Location : '. '<b>'.$_serviceDetails->service_location.'</b>'.'</br>
                                         Service Category : '. '<b>'.implode(',',ServiceCategory::getCategoryName($_serviceDetails->service_cat_id)).'</b>'.'</br>
                                    </td>
                                    <td>Vehicle: '. '<b>'.$_vehicleDetails['label'].'</b>'.'<br/>Vehicle make : '. '<b>'. VehicleMake::getMakeName($_vehicleDetails['vehicle_make']).'</b>' .'</br>
                                        Vehicle model : '. '<b>'.VehicleModel::getModelName($_vehicleDetails['vehicle_model']).'</b>'.'</br>
                                        Vehicle transmission : '. '<b>'.VehicleTransmission::getTransmissionName($_vehicleDetails['vehicle_transmission']).'</b>'.'</br>
                                        Vehicle class : '. '<b>'.VehicleClass::getClassName($_vehicleDetails['vehicle_class']).'</b>'.'</br>
                                    </td>
                                    <td>Title : '. '<b>'.$_locationDetails['label'].'</b>'.'<br/>Flat/Villa : '. '<b>'.$_locationDetails['house'].'</b>'.'</br>
                                        Block : '. '<b>'.$_locationDetails['block'].'</b>'.'</br>
                                        Road : '. '<b>'.$_locationDetails['road'].'</b>'.'</br>
                                        House : '. '<b>'.$_locationDetails['house'].'</b>'.'</br>
                                        Area : '. '<b>'.$_locationDetails['area'].'</b>'.'</br>
                                    </td>
                                    <td>
                                        Customer name : '. '<b>'.$userDetails['name'].'</b>'.'</br>
                                        Mobile : '. '<b>'.$userDetails['mobile'].'</b>'.'</br>
                                        Email : '. '<b>'.$userDetails['email'].'</b>'.'</br>
                                    </td>
                                    <td>
                                        Vendor name : '. '<b>'.$vendorDetails['name'].'</b>'.'</br>
                                        Mobile : '. '<b>'.$vendorDetails['mobile'].'</b>'.'</br>
                                        Email : '. '<b>'.$vendorDetails['email'].'</b>'.'</br>
                                    </td>
                                </tr>
                               </table>';
        //return  $_vehicleId.'-'.$id.'-'.$_locationId;
        return $html;
    }
}
