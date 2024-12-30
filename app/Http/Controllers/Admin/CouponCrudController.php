<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CouponRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\User;

/**
 * Class CouponCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CouponCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Coupon::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/coupon');
        CRUD::setEntityNameStrings('coupon', 'coupons');
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

         CRUD::addColumn(['name' => 'name', 'type' => 'text','label' => 'Coupon name']); 
         CRUD::addColumn(['name' => 'amount', 'type' => 'number','label' => 'Coupon amount']); 
         CRUD::addColumn(['name' => 'expiry', 'type' => 'text','label' => 'Coupon expiry']); 
         CRUD::addColumn(['name' => 'type', 'type' => 'text','label' => 'Coupon type']); 
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(CouponRequest::class);

        

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
        CRUD::addField([
            'name' => 'name',
            'type' => 'text',
            'label' => 'Coupon name',
            'wrapper'   => [
                'class' => 'form-group col-md-6'
              ]
        ]); 
        CRUD::addField([
            'name' => 'amount',
            'type' => 'number',
            'label' => 'Coupon amount',
            'wrapper'   => [
                'class' => 'form-group col-md-6'
              ]
        ]); 

        CRUD::addField([   // Date
            'name'  => 'expiry',
            'label' => 'Coupon expiry',
            'type'  => 'date',
            'wrapper'   => [
                'class' => 'form-group col-md-6'
              ]
        ]);
        CRUD::addField([
            'name' => 'min_spend',
            'type' => 'number',
            'label' => 'Minimum spend',
            'wrapper'   => [
                'class' => 'form-group col-md-6'
              ]
        ]); 
        CRUD::addField([
            'name' => 'max_spend',
            'type' => 'number',
            'label' => 'Maximum spend',
            'wrapper'   => [
                'class' => 'form-group col-md-6'
              ]
        ]); 

        CRUD::addField([  // Select
            'label'     => "Exclude Vendor",
            'type'      => 'select2_multiple',
            'name'      => 'exculde_vendor', // the db column for the foreign key
         
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
             'wrapper'   => [
                'class' => 'form-group col-md-6'
              ]
         ]);

         CRUD::addField([  // Select
            'label'     => "Exclude User",
            'type'      => 'select2_multiple',
            'name'      => 'exculde_user', // the db column for the foreign key
         
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
             'wrapper'   => [
                'class' => 'form-group col-md-6'
              ]
         ]);

         CRUD::addField([
            'name' => 'usage_limit',
            'type' => 'number',
            'label' => 'Usage limit',
            'wrapper'   => [
                'class' => 'form-group col-md-6'
              ]
        ]); 

        CRUD::addField([   // select2_from_array
            'name'        => 'type',
            'label'       => "Type",
            'type'        => 'select2_from_array',
            'options'     => ['percentage' => 'Percentage', 'fixed' => 'Fixed'],
            'default'     => 'fixed',
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
