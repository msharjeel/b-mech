<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\VehicleModelRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class VehicleModelCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class VehicleModelCrudController extends CrudController
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
        CRUD::setModel(\App\Models\VehicleModel::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/vehicle-model');
        CRUD::setEntityNameStrings('vehicle model', 'vehicle models');
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
            'name'          => 'name',
            'label'         => 'Name'
        ]); 
        CRUD::addColumn([
            'name'          => 'year',
            'label'         => 'Year'
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
        CRUD::setValidation(VehicleModelRequest::class);

        

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */

         CRUD::addField([  // Select
            'label'     => "Vehicle make",
            'type'      => 'select2',
            'name'      => 'make', // the db column for the foreign key
         
            // optional - manually specify the related model and attribute
            'model'     => "App\Models\VehicleMake", // related model
            'attribute' => 'name', // foreign key attribute that is shown to user
         
            // optional - force the related options to be a custom query, instead of all();
            'options'   => (function ($query) {
                 return $query->orderBy('name', 'ASC')->get();
             }), //  you can use this to filter the results show in the select
         ]);

        CRUD::addField([
            'name' => 'name',
            'label' => 'Model name', 
            'type' => 'text'
        ]); 

        CRUD::addField([  // Select
            'label'     => "Vehicle Class",
            'type'      => 'select2',
            'name'      => 'v_class', // the db column for the foreign key
         
            // optional - manually specify the related model and attribute
            'model'     => "App\Models\VehicleClass", // related model
            'attribute' => 'name', // foreign key attribute that is shown to user
         
            // optional - force the related options to be a custom query, instead of all();
            'options'   => (function ($query) {
                 return $query->orderBy('name', 'ASC')->get();
             }), //  you can use this to filter the results show in the select
         ]);

         CRUD::addField([  // Select
            'label'     => "Vehicle Transmission",
            'type'      => 'select2',
            'name'      => 'v_transmission', // the db column for the foreign key
         
            // optional - manually specify the related model and attribute
            'model'     => "App\Models\VehicleTransmission", // related model
            'attribute' => 'name', // foreign key attribute that is shown to user
         
            // optional - force the related options to be a custom query, instead of all();
            'options'   => (function ($query) {
                 return $query->orderBy('name', 'ASC')->get();
             }), //  you can use this to filter the results show in the select
         ]);

         CRUD::addField([  // Select
            'label'     => "Vehicle Cylinders",
            'type'      => 'select2',
            'name'      => 'v_cylinders', // the db column for the foreign key
         
            // optional - manually specify the related model and attribute
            'model'     => "App\Models\VehicleCylinder", // related model
            'attribute' => 'name', // foreign key attribute that is shown to user
         
            // optional - force the related options to be a custom query, instead of all();
            'options'   => (function ($query) {
                 return $query->orderBy('name', 'ASC')->get();
             }), //  you can use this to filter the results show in the select
         ]);

         CRUD::addField([  // Select
            'label'     => "Vehicle Drives",
            'type'      => 'select2',
            'name'      => 'v_drives', // the db column for the foreign key
         
            // optional - manually specify the related model and attribute
            'model'     => "App\Models\VehicleDrive", // related model
            'attribute' => 'name', // foreign key attribute that is shown to user
         
            // optional - force the related options to be a custom query, instead of all();
            'options'   => (function ($query) {
                 return $query->orderBy('name', 'ASC')->get();
             }), //  you can use this to filter the results show in the select
         ]);

         CRUD::addField([  // Select
            'label'     => "Vehicle Displacement",
            'type'      => 'select2',
            'name'      => 'v_displacements', // the db column for the foreign key
         
            // optional - manually specify the related model and attribute
            'model'     => "App\Models\VehicleDisplacement", // related model
            'attribute' => 'name', // foreign key attribute that is shown to user
         
            // optional - force the related options to be a custom query, instead of all();
            'options'   => (function ($query) {
                 return $query->orderBy('name', 'ASC')->get();
             }), //  you can use this to filter the results show in the select
         ]);

         CRUD::addField([  // Select
            'label'     => "Vehicle Country",
            'type'      => 'select2',
            'name'      => 'v_country', // the db column for the foreign key
         
            // optional - manually specify the related model and attribute
            'model'     => "App\Models\Country", // related model
            'attribute' => 'country_name', // foreign key attribute that is shown to user
         
            // optional - force the related options to be a custom query, instead of all();
            'options'   => (function ($query) {
                 return $query->orderBy('country_name', 'ASC')->get();
             }), //  you can use this to filter the results show in the select
         ]);

        CRUD::addField([
            'name' => 'year',
            'label' => 'Model year', 
            'type' => 'text'
        ]); 

        CRUD::addField([   // select_from_array
            'name'        => 'status',
            'label'       => "Status",
            'type'        => 'select_from_array',
            'options'     => ['1' => 'Active', '0' => 'De-Active'],
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
}
