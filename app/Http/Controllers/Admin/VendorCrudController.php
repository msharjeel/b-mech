<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\VendorRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Class VendorCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class VendorCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;

    protected function setupReorderOperation()
    {
        

        CRUD::addClause('whereHas', 'roles', function($query) {
           // $query->activePosts();
            $query->where('name', 'Vendor');
        });
        // define which model attribute will be shown on draggable elements
        CRUD::set('reorder.label', 'name');
        // define how deep the admin is allowed to nest the items
        // for infinite levels, set it to 0
        CRUD::set('reorder.max_level', 1);

        // if you don't fully trust the input in your database, you can set 
        // "escaped" to true, so that the label is escaped before being shown
        // you can also enable it globally in config/backpack/operations/reorder.php
        CRUD::set('reorder.escaped', true);
    }
    
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/vendor');
        CRUD::setEntityNameStrings('vendor', 'vendors');
        
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::removeButton('delete');
        $this->crud->removeButton('create');
        CRUD::removeButton('show');
       // $this->crud->removeAllButtons();
       // $this->crud->removeButton( 'preview' );
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */

         $this->crud->addClause('whereHas', 'roles', function($query) {
            $query->where('name', 'Vendor');
         });

         CRUD::addColumn([
            'name' => 'name',
            'label' => 'Name', 
            'type' => 'text'
        ]); 
        CRUD::addColumn([
            'name' => 'email',
            'label' => 'Email', 
            'type' => 'text'
        ]); 
        CRUD::addColumn([
            'name' => 'mobile',
            'label' => 'Mobile No.', 
            'type' => 'text'
        ]); 
        CRUD::addColumn([
            'name' => 'location',
            'label' => 'Location', 
            'type' => 'text'
        ]); 
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {   //echo '_iD'.$this->crud->getCurrentEntry()->id;
        CRUD::setValidation(VendorRequest::class);

  

        CRUD::addField([
            'name' => 'description', 
            'type' => 'textarea',
            'tab'             =>  'General'
        ]);

        CRUD::addField([
            'label'        => "Shop Image",
            'name'         => "image",
            'filename'     => "image_filename", // set to null if not needed
            'type'         => 'base64_image',
            'aspect_ratio' => 1, // set to 0 to allow any aspect ratio
            'crop'         => true, // set to true to allow cropping, false to disable
            'src'          => NULL, // null to read straight from DB, otherwise set to model accessor function
            'tab'             =>  'General'
        ]);

        CRUD::addField([
            'name' => 'latitude', 
            'type' => 'text',
            'tab'             =>  'General'
        ]);

        CRUD::addField([
            'name' => 'longitude', 
            'type' => 'text',
            'tab'             =>  'General'
        ]);
        CRUD::addField([
            'name' => 'mobile', 
            'type' => 'text',
            'label'         => 'Mobile number',   
            'tab'             =>  'General'
        ]);
        CRUD::addField([
            'name'          => 'service_duration', 
            'type'          => 'text',
            'label'         => 'Service duration',   
            'tab'           =>  'General'
        ]);
        CRUD::addField([
            'name'          => 'location', 
            'type'          => 'text',
            'label'         => 'Location',   
            'tab'           =>  'General'
        ]);
        CRUD::addField([
            'name'          => 'b_mechanic_comission', 
            'type'          => 'text',
            'label'         => 'B-Mechanic Comission',      
            'tab'           =>  'General'
        ]);

        CRUD::addField([
            'name'          => 'vendor_location_range', 
            'type'          => 'select2_multiple',
            'attribute'     => 'name', 
            'label'         => 'Location range',   
            'model'         => "App\Models\VendorLocation",   
            'tab'           => 'General'
        ]);

        CRUD::addField([   // select_from_array
            'name'        => 'status',
            'label'       => "Availability",
            'type'        => 'select_from_array',
            'options'     => ['0' => 'Not available', '1' => 'Available'],
            'allows_null' => false,
            'default'     => '1',
            'tab'             =>  'General'
            // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;
        ]);

        CRUD::addField([
            'name'          => 'meta',
            'type'          => "relationship",
            'label'          => "Services",
            'tab'             =>  'Services',
            'subfields'   => [
    
                [
                    'name' => 'user_id', 
                    'type' => 'hidden',
                    'value' => $this->crud->getCurrentEntry()->id,
                   
                    
                ],
                [
                    'name' => 'service_id',
                    'type' => 'select2',
                    'attribute' => 'service_title', 
                    'model'     => "App\Models\Service",
                    'label'  => "Service",
                    'wrapper' => [
                        'class' => 'form-group col-md-2',
                    ],
                ],
                [
                    'name' => 'country_id',
                    'type' => 'select2',
                    'attribute' => 'country_name', 
                    'model'     => "App\Models\Country",
                    'label'  => "Vehicle country",
                    'wrapper' => [
                        'class' => 'form-group col-md-2',
                    ],
                ],
                [
                    'name' => 'vehicle_class_id',
                    'type' => 'select2',
                    'attribute' => 'name', 
                    'model'     => "App\Models\VehicleClass",
                    'label'  => "Vehicle class",
                    'wrapper' => [
                        'class' => 'form-group col-md-2',
                    ],
                ],
                
                [
                    'name' => 'amount',
                    'type' => 'table',
                    'label'  => "Amount",
                    'columns'         => [
                        'form_year'  => 'From year',
                        'to_year'  => 'To year',
                        'price' => 'Price'
                    ],
                    'wrapper' => [
                        'class' => 'form-group col-md-6',
                    ],
                ]
            ],
        ]);
        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
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
        //CRUD::setValidation(VendorRequest::class);

    }

}
