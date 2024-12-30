<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Str;
use App\Models\ServiceCategory;

class Service extends Model
{
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'services';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    protected $casts = [
        'images'            => 'array',
        'service_cat_id'    => 'array',
        'vendor_id'         => 'array',
        'min_cost'          => 'decimal:3',
        'max_cost'          => 'decimal:3'
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    
    public function setIconImageAttribute($value)
    {
        $attribute_name = "icon_image";
        // or use your own disk, defined in config/filesystems.php
        $disk = config('backpack.base.root_disk_name');
        // destination path relative to the disk above
        $destination_path = "public/uploads/service";

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
            $image = \Image::make($value)->encode('png', 90);

            // 1. Generate a filename.
            $filename = md5($value.time()).'.png';

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

    public static function getServiceName( $id ){
        $name = Service::where('id',$id)->pluck('service_title')[0];
        return  $name;
    }

    public static function getCatID( $id ){

        $_id  = Service::where('id',$id)->pluck('service_id')[0];
        if( !empty($_id) ){
            //$name  = ServiceCategory::where('id',$_id)->pluck('cat_name')[0];
            return  $_id;
        }
    }
    
    public static function getServiceImage( $id ){
        $name = Service::where('id',$id)->pluck('icon_image')[0];
        return  $name;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
