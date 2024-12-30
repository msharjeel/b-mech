<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\VendorLocationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class VendorLocationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class VendorLocationCrudController extends CrudController
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
        CRUD::setModel(\App\Models\VendorLocation::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/vendor-location');
        CRUD::setEntityNameStrings('vendor location', 'vendor locations');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        
        CRUD::addColumn(['name' => 'name', 'type' => 'text']); 
        CRUD::addColumn(['name' => 'latitude', 'type' => 'text']); 
        CRUD::addColumn(['name' => 'longitude', 'type' => 'text']); 
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
        CRUD::setValidation(VendorLocationRequest::class);

        
        CRUD::addField(
            [
                'name' => 'name', 
                'type' => 'text',
                'label' => 'Location'
            ]
        ); 
        CRUD::addField(
            [
                'name' => 'latitude', 
                'type' => 'text',
                'label' => 'Latitude'
            ]
        ); 
        CRUD::addField(
            [
                'name' => 'longitude', 
                'type' => 'text',
                'label' => 'Longitude'
            ]
        ); 
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
