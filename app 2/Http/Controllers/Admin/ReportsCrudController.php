<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReportsRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ReportsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ReportsCrudController extends CrudController
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
        CRUD::setRoute(config('backpack.base.route_prefix') . '/reports');
        CRUD::setEntityNameStrings('reports', 'reports');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        
        $this->crud->removeAllButtons();

      

        CRUD::enableExportButtons();

        CRUD::filter('Status')
                ->type('dropdown')
                ->label('Service status')
                ->values(['0' => 'On the way', '1' => 'Cancelled', '2' => 'Ready to pickup your car','3' => 'Working on it', '4' => 'On the way back' , '5' => 'All done'])
        ->whenActive(function($value) {
            //print_r($value);exit;
            CRUD::addClause('where', 'status', '=', $value);
        })->apply();


        CRUD::filter('vendor_id')
        ->type('dropdown')
        ->label('Shop')
                ->values(function () {
                    return \App\Models\User::whereHas(
                                'roles', function($q){
                                    $q->where('name', 'Vendor');
                                }
                        )->pluck('name', 'id')->toArray();

                  })
        ->whenActive(function($value) {
            //print_r($value);exit;
            CRUD::addClause('where', 'vendor_id', '=', $value);
        })->apply();


        CRUD::filter('customer_id')
        ->type('dropdown')
        ->label('Customer')
                ->values(function () {
                    return \App\Models\User::whereHas(
                                'roles', function($q){
                                    $q->where('name', 'Customer');
                                }
                        )->pluck('name', 'id')->toArray();

                  })
        ->whenActive(function($value) {
            //print_r($value);exit;
            CRUD::addClause('where', 'customer_id', '=', $value);
        })->apply();

        CRUD::filter('from_to')
            ->type('date_range')
            // set options to customize, www.daterangepicker.com/#options
            ->date_range_options([
            'timePicker' => true // example: enable/disable time picker
            ])
            ->whenActive(function($value) {
                //print_r($value);
                $dates = json_decode($value);
                CRUD::addClause('where', 'created_at', '>=', $dates->from);
                CRUD::addClause('where', 'created_at', '<=', $dates->to);
            });

        CRUD::filter('status')
            ->type('dropdown')
            ->label('Order status')
            ->values([
                'pending'           => 'Pending',
                'processing'        => 'Processing',
                'complete'         => 'Completed',
                'cancel'            => 'Cancelled'
             
            ])
            ->whenActive(function($value) {
               // echo $value;
                CRUD::addClause('whereHas', 'orders', function($query) use($value) {
                    $query->where('status',$value);
                   });
        })->apply();

        CRUD::addColumn([
            // run a function on the CRUD model and show its return value
            'name'  => 'id',
            'label' => 'Order ID', // Table column heading
            'type'  => 'model_function',
            'function_name' => 'getOrderID', // the method in your Model
            // 'function_parameters' => [$one, $two], // pass one/more parameters to that method
            // 'limit' => 100, // Limit the number of characters shown
            // 'escaped' => false, // echo using {!! !!} instead of {{ }}, in order to render HTML
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('orders', function($query) use ($column,$searchTerm) {
                    $query->where('orders.id', 'like', '%'.$searchTerm.'%');
                });
            }

         ]);

      

        CRUD::addColumn([
            // run a function on the CRUD model and show its return value
            'name'  => 'customer_id',
            'label' => 'Customer Name', // Table column heading
            'type'  => 'model_function',
            'function_name' => 'getCustomerName', // the method in your Model
            // 'function_parameters' => [$one, $two], // pass one/more parameters to that method
            // 'limit' => 100, // Limit the number of characters shown
            // 'escaped' => false, // echo using {!! !!} instead of {{ }}, in order to render HTML
            //'searchLogic' => 'text'
         ]); 
       

         CRUD::addColumn([
            // run a function on the CRUD model and show its return value
            'name'  => 'vendor_id',
            'label' => 'Vendor Name', // Table column heading
            'type'  => 'model_function',
            'function_name' => 'getVendorName', // the method in your Model
            // 'function_parameters' => [$one, $two], // pass one/more parameters to that method
            // 'limit' => 100, // Limit the number of characters shown
            // 'escaped' => false, // echo using {!! !!} instead of {{ }}, in order to render HTML
         ]); 
       
         CRUD::addColumn([
            // run a function on the CRUD model and show its return value
            'name'  => 'service_id',
            'label' => 'Service Name', // Table column heading
            'type'  => 'model_function',
            'function_name' => 'getServiceName', // the method in your Model
            // 'function_parameters' => [$one, $two], // pass one/more parameters to that method
            // 'limit' => 100, // Limit the number of characters shown
            // 'escaped' => false, // echo using {!! !!} instead of {{ }}, in order to render HTML
         ]); 

         CRUD::addColumn([
            // run a function on the CRUD model and show its return value
            'name'  => 'vehicle_id',
            'label' => 'Vehicle Name', // Table column heading
            'type'  => 'model_function',
            'function_name' => 'getVehicleName', // the method in your Model
            // 'function_parameters' => [$one, $two], // pass one/more parameters to that method
            // 'limit' => 100, // Limit the number of characters shown
            // 'escaped' => false, // echo using {!! !!} instead of {{ }}, in order to render HTML
         ]); 

         CRUD::addColumn([
            // run a function on the CRUD model and show its return value
            'name'  => 'status',
            'label' => 'Service status', // Table column heading
            'type'  => 'model_function',
            'function_name' => 'getStatus', // the method in your Model
            // 'function_parameters' => [$one, $two], // pass one/more parameters to that method
            // 'limit' => 100, // Limit the number of characters shown
            // 'escaped' => false, // echo using {!! !!} instead of {{ }}, in order to render HTML
         ]);

         CRUD::addColumn([
            // run a function on the CRUD model and show its return value
            'name'  => 'amount',
            'label' => 'Order Amount', // Table column heading
            'type'  => 'model_function',
            'function_name' => 'getOrderAmount', // the method in your Model
            // 'function_parameters' => [$one, $two], // pass one/more parameters to that method
            // 'limit' => 100, // Limit the number of characters shown
            // 'escaped' => false, // echo using {!! !!} instead of {{ }}, in order to render HTML
         ]);

         CRUD::addColumn([
            // run a function on the CRUD model and show its return value
            'name'  => 'order_status',
            'label' => 'Order status', // Table column heading
            'type'  => 'model_function',
            'function_name' => 'getOrderStatus', // the method in your Model
            // 'function_parameters' => [$one, $two], // pass one/more parameters to that method
            // 'limit' => 100, // Limit the number of characters shown
            // 'escaped' => false, // echo using {!! !!} instead of {{ }}, in order to render HTML
         ]);
        
        // CRUD::addColumn(['name' => 'created_at', 'type' => 'date_time' ,'label' =>'Created At']); 

        CRUD::addColumn(
            [
                'name'  => 'created_at',
                'type' => 'datetime',
                'label' => 'Date',
                'format' => 'DD/MM/YYYY'
            ]
         );

         CRUD::addColumn(
            [
                'name'  => 'created_at',
                'type' => 'datetime',
                'key'       => 'converted_time',
                'label' => 'Time Received',
                'format' => 'h:mm A'
            ]
         );

         CRUD::addColumn(
            [
                'name'  => 'updated_at',
                'type' => 'datetime',
                'label' => 'Time Completed',
                'format' => 'h:mm A'
            ]
         );
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
        CRUD::setValidation(ReportsRequest::class);

        

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
