<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LocationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class LocationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class LocationCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Location::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/location');
        CRUD::setEntityNameStrings('location', 'locations');
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
            'name'          => 'user_id',
            'label'         => 'Customer name', 
            'type'          => 'model_function',
            'function_name' => 'getCustomerName', 
          
        ]); 
        
        CRUD::addColumn([
            'name' => 'label',
            'label' => 'Label', 
            'type' => 'text'
        ]); 
        CRUD::addColumn([
            'name' => 'city',
            'label' => 'City', 
            'type' => 'text'
        ]); 
        CRUD::addColumn([
            'name' => 'area',
            'label' => 'Area', 
            'type' => 'text'
        ]); 
        CRUD::addColumn([
            'name' => 'block',
            'label' => 'Block', 
            'type' => 'text'
        ]); 
        CRUD::addColumn([
            'name' => 'road',
            'label' => 'Road No.', 
            'type' => 'text'
        ]); 
        CRUD::addColumn([
            'name' => 'house',
            'label' => 'Home / Apartment', 
            'type' => 'text'
        ]); 
        CRUD::addColumn([
           // 'name'          => 'id',
            'label'         => 'Customer number', 
            'type'          => 'model_function',
            'function_name' => 'getCustomerMobileNo', 
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('users', function($query) use ($column,$searchTerm) {
                    $query->where('users.mobile', 'like', '%'.$searchTerm.'%');
                });
            }
           
        ]); 
        CRUD::addColumn([
            //'name'          => 'id',
            'label'         => 'Customer Email', 
            'type'          => 'model_function',
            'function_name' => 'getCustomerEmail', 
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
        CRUD::setValidation(LocationRequest::class);

        

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
            'name' => 'city',
            'label' => 'City', 
            'type' => 'text'
        ]); 
        CRUD::addField([
            'name' => 'area',
            'label' => 'Area', 
            'type' => 'text'
        ]); 
        CRUD::addField([
            'name' => 'block',
            'label' => 'Block', 
            'type' => 'text'
        ]); 
        CRUD::addField([
            'name' => 'road',
            'label' => 'Road No.', 
            'type' => 'text'
        ]); 
        CRUD::addField([
            'name' => 'house',
            'label' => 'Home / Apartment', 
            'type' => 'text'
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
                 return $query->orderBy('name', 'ASC')->get();
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
