<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SubCategory;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class ProductController extends Controller
{
    public function create()
    {
        $data = [];
        $category = Category::orderBy('name', 'ASC')->get();
        $brand = Brand::orderBy('name', 'ASC')->get();
        $data['category'] = $category;
        $data['brand'] = $brand;
        return view('admin.products.create', $data);
    }

    public function getSubCategory(Request $request)
    {
        if (!empty($request->category_id)) {
            $subCategory = SubCategory::where('category_id', $request->category_id)->orderBY('name', 'ASC')->get();
            return response()->json([
                'status' => true,
                'subCategory' => $subCategory
            ]);
        } else {
            return response()->json([
                'status' => false,
                'subCategory' => []
            ]);
        }
    }

    public function store(Request $request)
    {
        // dd($request->image_array);
        // exit();
        $rules =
            [
                'title' => 'required',
                'slug' => 'required|unique:products',
                'price' => 'required|numeric',
                'sku' => 'required|unique:products',
                'track_qty' => 'required|in:Yes,No',
                'category' => 'required|numeric',
                'is_featured' => 'required|in:Yes,No'

            ];
        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()){

            $product = new Product();
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->save();

            // Save Galleries
            if (!empty($request->image_array)){
                foreach($request->image_array as $temp_img_id){

                    $temp_img_info = TempImage::find($temp_img_id);
                    $textArray = explode('.',$temp_img_info->name);
                    $ext = last($textArray);

                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->image = 'NULL';
                    $productImage->save();

                    $product_img_name = $product->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                    $productImage->image = $product_img_name;
                    $productImage->save();

                    //product thumbnails
					
					//Large Image
                    $spath = public_path().'/temp/'.$temp_img_info->name;
                    $dpath = public_path().'/Uploads/Product/Large/'.$temp_img_info->name;
                    $manager = new ImageManager(new Driver());
                    $image = $manager->read($spath);
                    $image->resize(1400, null);
                    $image->save($dpath);
					
					//Small Image
                    $dpath = public_path().'/Uploads/Product/Small/'.$temp_img_info->name;
                    $image = $manager->read($spath);
                    $image->coverDown(300, 300);
                    $image->save($dpath);
                    
                    
                }
            }
            
            // session()->flash('success', 'Product Added !!');
            return response()->json([
                'status' => true,
                'message' => 'Product Added.'
            ]);

        }else{
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        }
    }
}
