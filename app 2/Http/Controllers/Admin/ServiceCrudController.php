<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ServiceRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\User;

/**
 * Class ServiceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ServiceCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

     use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;

    protected function setupReorderOperation()
    {
        // define which model attribute will be shown on draggable elements
        CRUD::set('reorder.label', 'service_title');
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
        CRUD::setModel(\App\Models\Service::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/service');
        CRUD::setEntityNameStrings('service', 'services');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
        CRUD::addColumn([
            'name' => 'service_title',
            'label' => 'Service Title', 
            'type' => 'text'
        ]); 
        // CRUD::addColumn([
        //     'name' => 'service_description', 
        //     'label' => 'Service description', 
        //     'type' => 'textarea'
        // ]); 
         CRUD::addColumn([
            'name' => 'min_cost', 
            'label' => 'Minimum cost', 
            'type' => 'number'
        ]); 
         CRUD::addColumn([
            'name' => 'max_cost', 
            'label' => 'Maximum cost', 
            'type' => 'number'
        ]); 
         CRUD::addColumn([
            'name' => 'service_duration', 
            'label' => 'Service Duration',
            'type' => 'number'
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
        CRUD::setValidation(ServiceRequest::class);

        

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */

         CRUD::addField([
            'name' => 'service_title', 
            'label' => 'Service Title', 
            'type' => 'text'
        ]); 

        CRUD::addField([
            'name'      => 'service_id',
            'label'     => 'Service category', 
            'type'      => 'select2',
            'attribute' => 'cat_name',
            'model'     => 'App\Models\ServiceCategory',
            'options'   => (function ($query) {
                return $query->orderBy('cat_name', 'ASC')
                                ->where('parent_id','=', null)
                                ->get();
            }),
        ]); 

        // CRUD::addField( [
        //     // n-n relationship (with pivot table)
        //     'label'                     => 'Service category', // Table column heading
        //     'type'                      => 'select2_multiple',
        //     'name'                      => 'service_cat_id', 
        //     'attribute'                 => 'cat_name',
        //     'allows_multiple'           => true,
        //     'dependencies'              => ['service_id'],
        //     'model'                     => 'App\Models\ServiceCategory', 
        //     'options'                   => (function ($query) {
        //         return $query->orderBy('cat_name', 'ASC')
        //                         ->where('parent_id','!=', null)
        //                         ->get();
        //     }),
        //  ]); 
       
        CRUD::addField([
            'name' => 'service_description', 
            'label' => 'Service description', 
            'type' => 'textarea'
        ]); 
        CRUD::addField([
            'name'          => 'icon_image',
            'label'         => 'Service Icon Image',
            'type'          => 'image',
            'multiple'      => true, // enable/disable the multiple selection functionality
            'sortable'      => true,
            
    ]);
        CRUD::addField([
                'name'          => 'images',
                'label'         => 'Service Image',
                'type'          => 'browse_multiple',
                'multiple'      => true, // enable/disable the multiple selection functionality
                'sortable'      => true,
                
        ]);
         CRUD::addField([
            'name' => 'min_cost', 
            'label' => 'Minimum cost', 
            'type' => 'number',
            'attributes' => ["step" => "any"],
            //'suffix'     => ".000"
        ]); 
         CRUD::addField([
            'name' => 'max_cost', 
            'label' => 'Maximum cost', 
            'type' => 'number',
            'attributes' => ["step" => "any"],
            //'suffix'     => ".000"
        ]); 
        CRUD::addField([   // select_from_array
            'name'        => 'type',
            'label'       => "Service Type",
            'type'        => 'select_from_array',
            'options'     => ['on-site' => 'On-Site', 'off-site' => 'Off-site'],
            'allows_null' => false,
            'default'     => 'on-site',
            // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;
        ]);

         CRUD::addField([
            'name' => 'service_duration', 
            'label' => 'Service Duration',
            'type' => 'number'
        ]); 

        CRUD::addField([   // select_from_array
            'name'        => 'status',
            'label'       => "Status",
            'type'        => 'select_from_array',
            'options'     => ['1' => 'Enable', '0' => 'Disable'],
            'allows_null' => false,
            'default'     => '1',
            // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;
        ]);

        // CRUD::addField([  // Select
        //     'label'     => "Vendor",
        //     'type'      => 'select2_multiple',
        //     'name'      => 'vendor_id', // the db column for the foreign key
         
        //     // optional - manually specify the related model and attribute
        //     'model'     => "App\Models\User", // related model
        //     'attribute' => 'name', // foreign key attribute that is shown to user
         
        //     // optional - force the related options to be a custom query, instead of all();
        //     'options'   => (function ($query) {
        //          return User::whereHas(
        //                         'roles', function($q){
        //                             $q->where('name', 'Vendor');
        //                         }
        //                 )->get();
        //      }), //  you can use this to filter the results show in the select
        //  ]);
       
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
