<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CustomerVehicleRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\User;
/**
 * Class CustomerVehicleCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CustomerVehicleCrudController extends CrudController
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
        CRUD::setModel(\App\Models\CustomerVehicle::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/customer-vehicle');
        CRUD::setEntityNameStrings('customer vehicle', 'customer vehicles');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        
        CRUD::enableExportButtons();
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
            CRUD::addColumn([
                'name'          => 'label',
                'label'         => 'Vehicle Label', 
            ]); 

            CRUD::addColumn([
                'name'          => 'vehicle_make',
                'label'         => 'Vehicle make', 
                'type'          => 'model_function',
                'function_name' => 'getVehicleMake', 
            ]); 

            CRUD::addColumn([
                'name'          => 'vehicle_model',
                'label'         => 'Vehicle model', 
                'type'          => 'model_function',
                'function_name' => 'getVehicleModel', 
            ]); 

            CRUD::addColumn([
                'name'          => 'vehicle_year',
                'label'         => 'Vehicle year', 
            ]); 

            CRUD::addColumn([
                'name'          => 'user_id',
                'label'         => 'Customer name', 
                'type'          => 'model_function',
                'function_name' => 'getCustomerName', 
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
        CRUD::setValidation(CustomerVehicleRequest::class);

        

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */

        CRUD::addField([
            'name' => 'label',
            'label' => 'Label', 
            'type' => 'text'
        ]); 

        CRUD::addField([  
            'label'     => "Vehicle make",
            'type'      => 'select',
            'name'      => 'vehicle_make',
            'model'     => "App\Models\VehicleMake", 
            'attribute' => 'name',
            'options'   => (function ($query) {
                 return $query->orderBy('name', 'ASC')->get();
             }),
         ]);

         CRUD::addField([ 
            'label'             => "Vehicle Model",
            'type'              => 'select2_from_ajax',
            'name'              => 'vehicle_model', 
            'data_source'       => url("v1/api/model/"),
            
            'model'             => "App\Models\VehicleModel", 
            'attribute'         => 'name', 
            'method'            => 'POST',
            'dependencies'      => ['vehicle_make'],
          
            'options'   => (function ($query) {
                 return $query->orderBy('name', 'ASC')->get();
             }),
         ]);
         
         CRUD::addField([
            'name' => 'vehicle_year',
            'label' => 'Vehicle Year', 
            'type' => 'text'
        ]); 

        CRUD::addField([  
            'label'     => "Vehicle Transmission",
            'type'      => 'select',
            'name'      => 'vehicle_transmission',
            'model'     => "App\Models\VehicleTransmission", 
            'attribute' => 'name',
            'options'   => (function ($query) {
                 return $query->orderBy('name', 'ASC')->get();
             }),
         ]);

         CRUD::addField([  
            'label'     => "Vehicle Drive",
            'type'      => 'select',
            'name'      => 'vehicle_drive',
            'model'     => "App\Models\VehicleDrive", 
            'attribute' => 'name',
            'options'   => (function ($query) {
                 return $query->orderBy('name', 'ASC')->get();
             }),
         ]);

         CRUD::addField([  
            'label'     => "Vehicle displacemnet",
            'type'      => 'select',
            'name'      => 'vehicle_displacement',
            'model'     => "App\Models\VehicleDisplacement", 
            'attribute' => 'name',
            'options'   => (function ($query) {
                 return $query->orderBy('name', 'ASC')->get();
             }),
         ]);

         CRUD::addField([  
            'label'     => "Vehicle Cylinder",
            'type'      => 'select',
            'name'      => 'vehicle_cylinder',
            'model'     => "App\Models\VehicleCylinder", 
            'attribute' => 'name',
            'options'   => (function ($query) {
                 return $query->orderBy('name', 'ASC')->get();
             }),
         ]);

         CRUD::addField([  
            'label'     => "Vehicle class",
            'type'      => 'select',
            'name'      => 'vehicle_class',
            'model'     => "App\Models\VehicleClass", 
            'attribute' => 'name',
            'options'   => (function ($query) {
                 return $query->orderBy('name', 'ASC')->get();
             }),
         ]);
         CRUD::addField([  // Select
            'label'     => "User",
            'type'      => 'select',
            'name'      => 'user_id', // the db column for the foreign key
         
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
}
