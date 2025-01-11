<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;


class ProductImageController extends Controller
{
    public function update(Request $request)
    {
        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $sourcePath = $image->getPathName();

        $productImage = new ProductImage();
        $productImage->product_id = $request->product_id;
        $productImage->image = 'NULL';
        $productImage->save();

        $imageName = $request->product_id . '-' . $productImage->id . '-' . time() . '.' . $ext;
        $productImage->image = $imageName;
        $productImage->save();


        //Large Image
        $dpath = public_path() . '/Uploads/Product/Large/' . $imageName;
        $manager = new ImageManager(new Driver());
        $image = $manager->read($sourcePath);
        $image = $image->scale(height: 300);
        $image->save($dpath);

        //Small Image
        $dpath = public_path() . '/Uploads/Product/Small/' . $imageName;
        $image = $manager->read($sourcePath);
        $image->coverDown(300, 300);
        $image->save($dpath);

        return response()->json([
            'status' => true,
            'message' => 'Image Uploaded.',
            'image_id' => $productImage->id,
            'imagePath' => asset('Uploads/Product/Small/' . $productImage->image)
        ]);
    }

    public function delete(Request $request)
    {
        $productImage = ProductImage::find($request->id);

        if (empty($productImage)){
            return response()->json([
                'status' => false,
                'message' =>'Image not found.'
            ]);
        }

        //Delete Image from Folder
        File::delete(public_path('Uploads/Product/Large/' . $productImage->image));
        File::delete(public_path('Uploads/Product/Small/' . $productImage->image));
        $productImage->delete();

        return response()->json([
            'status' => true,
            'message' =>'Image Deleted.'
        ]);
    }
}
