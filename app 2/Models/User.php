<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait; 
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\Order;
use App\Models\ServiceRequest;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, CrudTrait, HasRoles;

   
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'location',
        'description',
        'image',
        'latitude',
        'longitude',
        'status',
        'b_mechanic_comission',
        'service_duration',
        'location',
        'fcm_token',
        "vendor_location_range"
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'vendor_location_range' => 'array'
    ];

    protected $guard_name = 'backpack';

    public function setImageAttribute($value)
    {
        $attribute_name = "image";
        // or use your own disk, defined in config/filesystems.php
        $disk = config('backpack.base.root_disk_name');
        // destination path relative to the disk above
        $destination_path = "public/uploads/shop";

        // if the image was erased
        if (empty($value)) {
            // delete the image from disk
            if (isset($this->{$attribute_name}) && !empty($this->{$attribute_name})) {
                \Storage::disk($disk)->delete($this->{$attribute_name});  
            }
            // set null on database column
            $this->attributes[$attribute_name] = null;
        }

        // if a base64 was sent, store it in the db
        if (Str::startsWith($value, 'data:image'))
        {
            // 0. Make the image
            $image = \Image::make($value)->encode('jpg', 90);

            // 1. Generate a filename.
            $filename = md5($value.time()).'.jpg';

            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path.'/'.$filename, $image->stream());

            // 3. Delete the previous image, if there was one.
            if (isset($this->{$attribute_name}) && !empty($this->{$attribute_name})) {
                \Storage::disk($disk)->delete($this->{$attribute_name});
            }

            // 4. Save the public path to the database
            // but first, remove "public/" from the path, since we're pointing to it
            // from the root folder; that way, what gets saved in the db
            // is the public URL (everything that comes after the domain name)
            $public_destination_path = Str::replaceFirst('public/', '', $destination_path);
            $this->attributes[$attribute_name] = $public_destination_path.'/'.$filename;
        } elseif (!empty($value)) {
            // if value isn't empty, but it's not an image, assume it's the model value for that attribute.
            $this->attributes[$attribute_name] = $this->{$attribute_name};
        }
    }

    //Relation
    public function meta(){
        return $this->hasMany('App\Models\UsersMeta');
    }

    public function orders(){
        return $this->hasMany('App\Models\Order','status','id');
    }

    public function transactions(){
        return $this->hasMany('App\Models\Transaction');
    }

    public function requests(){
        return $this->hasMany('App\Models\ServiceRequest','vendor_id');
    }

    public function getOrderCount() {
        return User::find($this->id)->requests()
                        ->count();
    }

    public function getOrderAmount(){
        $id      = User::find($this->id)->requests()
                        ->select('id')->get()->toArray();
        if( !empty( $id ) ){
           $orderValue  = Order::whereIn('service_request_id',$id)
                                ->select(DB::raw('SUM(order_amount) as total_sales'))
                                ->get()->toArray();
           return number_format($orderValue[0]['total_sales'],3). ' BHD';

        }else{
            return '-';
        }
    }


    
   
}
