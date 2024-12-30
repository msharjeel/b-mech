<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Help;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class HelpController extends Controller
{
    //
    public function _helpRequest( Request $request , $id = null ){

        $request->validate([
            'name'          =>  'required',
            'email'         =>  'required',
            'message'       =>  'required',
        ]);

        if($request->has('image') && $request->image != '' ) {
            $imagepath  =  $this->convertImage( $request->image );
        }else{
            $imagepath  = null;
        }

        $_help  = new Help();
        $_help->name            =  $request->name;
        $_help->email           =  $request->email;
        $_help->message         =  $request->message;
        $_help->attachment      =  $imagepath;
        $_help->user_id         =  $id;
       //$imagepath  =  $this->convertImage( $request->image );
       //echo $imagepath;

       if( $_help->save() ){
        return response()->json([
            'status'        => true,
            'message'       => 'Successfully addded!!!',
          //  'data'          => $_servicesDetail,
        ],200);
    }else{
        return response()->json([
            'status'        => true,
            'message'       => 'Something wen wrong!!!',
           // 'data'          => $_servicesDetail,
        ],200);
    }
    }


    private function convertImage( $image = null ){

        $disk = config('backpack.base.root_disk_name');
        // destination path relative to the disk above
        $destination_path = "public/uploads/helpsupport";

       // if (Str::startsWith($image, 'data:image'))
       // {
            // 0. Make the image
            $image = \Image::make($image)->encode('jpeg', 90);

            // 1. Generate a filename.
            $filename = md5($image.time()).'.jpeg';

            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path.'/'.$filename, $image->stream());

            $public_destination_path = Str::replaceFirst('public/', '', $destination_path);
            return $public_destination_path.'/'.$filename;
      //  }

    }
}
