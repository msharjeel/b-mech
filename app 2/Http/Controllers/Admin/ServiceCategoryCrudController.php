<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ServiceCategoryRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Class ServiceCategoryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ServiceCategoryCrudController extends CrudController
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
       CRUD::set('reorder.label', 'cat_name');
        // define how deep the admin is allowed to nest the items
        // for infinite levels, set it to 0
         CRUD::set('reorder.max_level', 0);

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
        CRUD::setModel(\App\Models\ServiceCategory::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/service-category');
        CRUD::setEntityNameStrings('service category', 'service categories');
        
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        if( !backpack_user()->hasRole('Super Admin')){
            $this->crud->addClause('where', 'vendor_id', '=', backpack_user()->id );
        }
       // CRUD::addClause('where', 'parent_id', '!=', NULL);
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
        // CRUD::addColumn(['name' => 'cat_name', 'type' => 'text']); 
        CRUD::addColumn(
                [
                    'name' => 'cat_name', 
                    'type' => 'text',
                    
                ]
        ); 

        CRUD::addColumn(
            [
                'name'      => 'vendor_id', 
                'type'      => 'closure',
                'label'     => 'User/Vendor',
                'function' => function($entry) {
                    return Str::of(User::find($entry->vendor_id)->name)->ucfirst();
                }
            ]
        ); 
         
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ServiceCategoryRequest::class);

        

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */

         CRUD::addField([
            'name'      => 'parent_id', 
            'type'      => 'select', 
            'label'     => "Service Parent",
            //'entity'    => 'service_categories',
            'model'     => "App\Models\ServiceCategory",
            'attribute' => 'cat_name',

            'options'   => (function ($query) {
                if( !backpack_user()->hasRole('Super Admin')){
                    return $query->orderBy('cat_name', 'ASC')
                                ->where('parent_id','=', null)
                                ->where('vendor_id','=', backpack_user()->id)
                                ->get();
                }else{
                    return $query->orderBy('cat_name', 'ASC')
                            ->where('parent_id','=', null)
                            //->where('vendor_id','=', backpack_user()->id)
                            ->get();
                }
            }), 
        ]);

        CRUD::addField([
            'label'        => "Category image",
            'name'         => "image",
            'filename'     => "image_filename", // set to null if not needed
            'type'         => 'image',
            'aspect_ratio' => 1, // set to 0 to allow any aspect ratio
            'crop'         => true, // set to true to allow cropping, false to disable
            'src'          => NULL, // null to read straight from DB, otherwise set to model accessor function
            //'tab'             =>  'General'
        ]);

        CRUD::addField([
            'name'      => 'cat_name', 
            'type'      => 'text', 
            'label'     => "Service  name"
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

        CRUD::addField(
            [   // Hidden
                'name'  => 'vendor_id',
                'type'  => 'hidden',
                'value' => backpack_user()->id,
            ],
        );
       
        
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
