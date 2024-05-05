<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
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
        $rules =
            [
                'title' => 'required',
                'slug' => 'required',
                'price' => 'required|numeric',
                'sku' => 'required',
                'track_qty' => 'required|in:Yes,No',
                'category' => 'required|numeric',
                'is_featured' => 'required|in:Yes,No'

            ];
        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()){

        }else{
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        }
    }
}
