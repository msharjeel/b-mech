<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequestLogRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class UserRequestLogCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserRequestLogCrudController extends CrudController
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
       

        CRUD::setModel(\App\Models\UserRequestLog::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user-request-log');
        CRUD::setEntityNameStrings('user request log', 'user request logs');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        $this->crud->addClause('where', 'accepted_by', '=', NULL);
       // $this->crud->addClause('where', 'rejected_by', '=', '');
        CRUD::removeButton('delete');
        $this->crud->removeButton('create');
        CRUD::removeButton('show');
        CRUD::removeButton('update');
        CRUD::enableExportButtons();

        CRUD::addColumn(['name' => 'user_id', 'type' => 'text','label' => 'CustomerName',
            'type'  => 'model_function',
            'function_name' => 'getCustomerName',
            ]
        ); 
        CRUD::addColumn(['name' => 'vendor_id', 'type' => 'text','label' => 'VendorName',
                            'type'  => 'model_function',
                            'function_name' => 'getVendorName',
                            ]
                        ); 
        CRUD::addColumn(['name' => 'service_id', 'type' => 'text','label' => 'ServiceName',
                            'type'  => 'model_function',
                            'function_name' => 'getServiceName',
                            ]
                        ); 
      //  CRUD::column('year');
      CRUD::addColumn(['name' => 'rejected_by', 'type' => 'custom_html','label' =>'Reason',
                        'type'  => 'model_function',
                        'function_name' => 'getAcceptedName',
                        ]
                    ); 
      
      
       // CRUD::column('created_at');
        CRUD::addColumn(['name' => 'created_at', 'type' => 'datetime','label' => 'Date']); 
        //CRUD::column('updated_at');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(UserRequestLogRequest::class);

        CRUD::field('user_id');
        CRUD::field('vendor_id');
        CRUD::field('service_id');
        CRUD::field('year');
        CRUD::field('accepted_by');
        CRUD::field('rejected_by');

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
    }
}
