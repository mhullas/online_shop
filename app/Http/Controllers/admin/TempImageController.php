<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class TempImageController extends Controller
{
    public function tempImage(Request $request){
        $image = $request->image;

        if (!empty($image)){
            $ext = $image->getClientOriginalExtension();
            $newName = time().'.'.$ext;
            
            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();
            $image->move(public_path().'/temp_images', $newName);

            //Generate thumbnail
            $sourcePath = public_path().'/temp_images/'.$newName;
            $destPath = public_path().'/temp_images/thumb/'.$newName;
            $manager = new ImageManager(new Driver());
            $image = $manager->read($sourcePath);
            $image = $image->scale(300, 250);
            $image->save($destPath);


            return response()->json([
                'status' => true,
                'image_id' => $tempImage->id,
                'imagePath' => asset('/temp_images/thumb/'.$newName),
                'message' => 'Image Uploaded Successfully.'
            ]);
            
        }
    }
}
