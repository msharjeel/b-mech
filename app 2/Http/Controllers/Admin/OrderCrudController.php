<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrderRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\ServiceRequest;
use App\Models\Order;
/**
 * Class OrderCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class OrderCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }


    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Order::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/order');
        CRUD::setEntityNameStrings('order', 'orders');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        
        CRUD::addColumn(['name' => 'id', 'type' => 'text','label' => 'Order Id']); 
        CRUD::addColumn(['name' => 'order_amount', 'type' => 'number','label' => 'Amount']); 
        //CRUD::addColumn(['name' => 'paid_status', 'type' => 'text','label' => 'Status']); 
        CRUD::addColumn(['name' => 'payment_through', 'type' => 'text','label' => 'Payment type']); 
        CRUD::addColumn(['name' => 'status', 'type' => 'text','label' => 'Status']); 
        CRUD::addColumn(['name' => 'vat_amount', 'type' => 'text','label' => 'Vat']); 
        CRUD::addColumn(['name' => 'b_mechanic', 'type' => 'text','label' => 'B-Mechanic %']);

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
        CRUD::setValidation(OrderRequest::class);

        
        CRUD::addField([   // select_from_array
            'name'        => 'status',
            'label'       => "Order status",
            'type'        => 'select_from_array',
            'options'     => ['pending' => 'Pending', 'processing' => 'Processing', 'complete' => 'Completed', 'cancel' => 'Cancel', 'refund' => 'Refund'],
            'allows_null' => false,
            'default'     => 'pending',
            // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;
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
        // ServiceRequest::updating(function($entry) {
        //     $entry->status = 2;
        //  //   print_r($entry);exit;
        // });

        $this->setupCreateOperation();

       
    }

    public function update()
    {
        $parameters     = \Route::current()->parameters();
        $request        =    \Request::all();
    
        if( $request['status'] =='cancel' ){
           // echo $request['status'];exit;
            $serviceId          =   Order::where('id',$parameters['id'])->select('service_request_id')->get()->toArray()[0];
            $serviceRequest     =   ServiceRequest::find($serviceId['service_request_id']);
            $serviceRequest->status = 1;
            $serviceRequest->save();
        }
      

     

        // do something before validation, before save, before everything; for example:
        // $this->crud->addField(['type' => 'hidden', 'name' => 'author_id']);
        // $this->crud->removeField('password_confirmation');

        // Note: By default Backpack ONLY saves the inputs that were added on page using Backpack fields.
        // This is done by stripping the request of all inputs that do NOT match Backpack fields for this
        // particular operation. This is an added security layer, to protect your database from malicious
        // users who could theoretically add inputs using DeveloperTools or JavaScript. If you're not properly
        // using $guarded or $fillable on your model, malicious inputs could get you into trouble.

        // However, if you know you have proper $guarded or $fillable on your model, and you want to manipulate 
        // the request directly to add or remove request parameters, you can also do that.
        // We have a config value you can set, either inside your operation in `config/backpack/crud.php` if
        // you want it to apply to all CRUDs, or inside a particular CrudController:
            // $this->crud->setOperationSetting('saveAllInputsExcept', ['_token', '_method', 'http_referrer', 'current_tab', 'save_action']);
        // The above will make Backpack store all inputs EXCEPT for the ones it uses for various features.
        // So you can manipulate the request and add any request variable you'd like.
        // $this->crud->getRequest()->request->add(['author_id'=> backpack_user()->id]);
        // $this->crud->getRequest()->request->remove('password_confirmation');
        // $this->crud->getRequest()->request->add(['author_id'=> backpack_user()->id]);
        // $this->crud->getRequest()->request->remove('password_confirmation');

        $response = $this->traitUpdate();
        // do something after save
        return $response;
    }
}
