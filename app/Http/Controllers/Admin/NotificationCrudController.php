<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\NotificationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\User;

/**
 * Class NotificationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class NotificationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Notification::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/notification');
        CRUD::setEntityNameStrings('notification', 'notifications');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        
        CRUD::addColumn(['name' => 'title', 'type' => 'text']);
        CRUD::addColumn(['name' => 'description', 'type' => 'text']);

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
        CRUD::setValidation(NotificationRequest::class);


        CRUD::addField([  // Select
            'label'     => "Customer",
            'type'      => 'select2_multiple',
            'name'      => 'users', // the db column for the foreign key
         
            // optional - manually specify the related model and attribute
            'model'     => "App\Models\User", // related model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'select_all' => true,
            // optional - force the related options to be a custom query, instead of all();
            'options'   => (function ($query) {
                 return User::whereHas(
                                'roles', function($q){
                                    $q->where('name', 'Customer');
                                }
                        )->get();
             }), //  you can use this to filter the results show in the select
             'wrapper' => [
                'class' => 'col-md-6',
            ],
         ]);

        CRUD::addField([  // Select
            'label'     => "Shop",
            'type'      => 'select2_multiple',
            'name'      => 'vendors', // the db column for the foreign key
         
            // optional - manually specify the related model and attribute
            'model'     => "App\Models\User", // related model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'select_all' => true,
            // optional - force the related options to be a custom query, instead of all();
            'options'   => (function ($query) {
                 return User::whereHas(
                                'roles', function($q){
                                    $q->where('name', 'Vendor');
                                }
                        )->get();
             }), //  you can use this to filter the results show in the select
             'wrapper' => [
                'class' => 'col-md-6',
            ],
         ]);

         CRUD::addField([
            'name' => 'title', 
            'label' => 'Notification Title', 
            'type' => 'text',
            'wrapper' => [
                'class' => 'col-md-6',
            ],
        ]); 
        
         CRUD::addField([
            'name' => 'description', 
            'label' => 'Notification description', 
            'type' => 'textarea',
            'wrapper' => [
                'class' => 'col-md-6',
            ],
        ]); 

        CRUD::addField([   // select_from_array
            'name'        => 'user_type',
            'label'       => "Send to",
            'type'        => 'select_from_array',
            'options'     => ['Customer' => 'Customer', 'Vendor' => 'Vendor'],
            'allows_null' => false,
            'default'     => 'Customer',
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
        $this->setupCreateOperation();
    }


    private function senPushNotification( $token = [], $title , $type , $body = ''){
            
        //  $SERVER_API_KEY = "AAAABnKch4w:APA91bEeYiDRzKvbvsYbw6mq_uVYm59It7auG1pQBg6-K0_oZDtihauW32w2bKvME-5MwD6xqsW4T5j-x9tk8k5BtJVH5i9p4iisQkU-m3ZkwVb0d2cqR0W0qBR_nFzhEBL9iboqu7OK";
        if( $type =='Vendor' ){
            $SERVER_API_KEY   = env('VENDOR_SERVER_API_KEY');
        }else{
            $SERVER_API_KEY   = env('USER_SERVER_API_KEY');
        }
         
          $data = [
              "registration_ids" => $token,
              "notification" => [
                  "title"                 =>  $title,
                  "body"                  =>  $body,
                  "priority"              =>  "high",
                  "content_available"     =>  true,
              ],
              "data"  => [
                  "screen"                => "Notifications",
                  "priority"              => "high",
                  
              ]
          ];

    

          $dataString = json_encode($data);
      
          $headers = [
              'Authorization: key=' . $SERVER_API_KEY,
              'Content-Type: application/json',
          ];
      
          $ch = curl_init();
      
          curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
              
          $response = curl_exec($ch);

          // Log details
    \Log::info('FCM Payload', ['data' => $data]);
    \Log::info('FCM Response', ['response' => $response]);
  
        // dd($response);
  }

  public function update(){
        $parameters     = \Route::current()->parameters();
        $request        =    \Request::all();
       // print_r($request);exit;
        $userIDS        = array_merge(( !empty($request['users']) ) ? $request['users'] : [], ( !empty($request['vendors']) ) ? $request['vendors'] : [] );
       
        if( !empty($userIDS) ){
            $fcmToken        = User::whereIn('id',  $userIDS)
                                ->pluck('fcm_token')
                                ->toArray();
        }else{
            $fcmToken        = User::pluck('fcm_token')
                                ->toArray();
        }
       //print_r(array_filter($fcmToken) );exit;
        // if( !empty($fcmToken) )
        $this->senPushNotification(array_values(array_filter($fcmToken)),$request['title'],$request['user_type'],$request['description']);
        // print_r($fcmToken);
        // print_r($request);exit;

     

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

    public function store()
    {
        $parameters     = \Route::current()->parameters();
        $request        =    \Request::all();
    
        $userIDS        = array_merge(( !empty($request['users']) ) ? $request['users'] : [], ( !empty($request['vendors']) ) ? $request['vendors'] : [] );
        if( !empty($userIDS) ){
            $fcmToken        = User::whereIn('id',  $userIDS)
                                ->pluck('fcm_token')
                                ->toArray();
        }else{
            $fcmToken        = User::pluck('fcm_token')
                                ->toArray();
        }
        // if( !empty($fcmToken) ){

        // }
            $this->senPushNotification(array_values(array_filter($fcmToken)),$request['title'],$request['user_type'],$request['description']);

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

        $response = $this->traitStore();
        // do something after save
        return $response;
    }

}
