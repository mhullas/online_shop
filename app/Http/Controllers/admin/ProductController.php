<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        dd($request->image_array);
        exit();
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

            //generate thubmnails

            
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
