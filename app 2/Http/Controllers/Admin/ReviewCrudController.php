<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReviewRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ReviewCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ReviewCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Review::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/review');
        CRUD::setEntityNameStrings('review', 'reviews');
    
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
            'name'          => 'vendor_id',
            'type'          => 'model_function',
            'label'         => 'Vendor name', 
            'function_name' => 'getVendorName', 
        ]); 
        CRUD::addColumn([
            'name'          => 'seervice_id',
            'label'         => 'Service Name', 
            'type'          => 'model_function',
            'function_name' => 'getServiceName',
        ]); 
        CRUD::addColumn([
            'name'      => 'order_id',
            'label'     => 'Order ID', 
            'type'      => 'text'
        ]); 
        CRUD::addColumn([
            'name'      => 'rating',
            'label'     => 'Rating', 
            'type'      => 'text'
        ]); 
        CRUD::addColumn([
            'name'      => 'review',
            'label'     => 'Review comment', 
            'type'      => 'text'
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
        CRUD::setValidation(ReviewRequest::class);

        

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */

         CRUD::addField([   // select_from_array
            'name'        => 'status',
            'label'       => "Aprrove",
            'type'        => 'select_from_array',
            'options'     => ['1' => 'Approve', '0' => 'Reject'],
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
