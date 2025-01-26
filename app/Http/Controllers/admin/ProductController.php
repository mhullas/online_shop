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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::latest()->with('product_images');
            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = '<a href="' . route('product.edit', $data->id) . '">
                                                <svg class="filament-link-icon w-4 h-4 mr-1"
                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                    fill="currentColor" aria-hidden="true">
                                                    <path
                                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                                    </path>
                                                </svg>
                                            </a>';
                    $btn .= '<a href="javascript://" data-record-id="' . $data->id . '"
                                                data-record-title="' . $data->title . '" data-record-tag="product" data-toggle="modal"
                                                data-target="#confirm_delete" class="text-danger w-4 h-4 mr-1">
                                                <svg wire:loading.remove.delay="" wire:target=""
                                                    class="filament-link-icon w-4 h-4 mr-1"
                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                    fill="currentColor" aria-hidden="true">
                                                    <path ath fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.products.list');


        // $products = Product::latest()->with('product_images');
        // if ($request->get('keyword') != "") {
        //     $products = $products->where('title', 'like', '%' . $request->keyword . '%');
        // }
        // $products = $products->paginate();
        // $data['products'] = $products;
        // return view('admin.products.list', $data);
    }

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
        if ($validator->passes()) {

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
            // $ullas = $request->image_array;
            // dd($ullas);
            if (!empty($request->image_array)) {
                foreach ($request->image_array as $temp_img_id) {

                    $temp_img_info = TempImage::find($temp_img_id);
                    $textArray = explode('.', $temp_img_info->name);
                    $ext = last($textArray);

                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->image = 'NULL';
                    $productImage->save();

                    $product_img_name = $product->id . '-' . $productImage->id . '-' . time() . '.' . $ext;
                    $productImage->image = $product_img_name;
                    $productImage->save();

                    //product thumbnails
                    //Large Image
                    $spath = public_path() . '/temp_images/thumb/' . $temp_img_info->name;
                    $dpath = public_path() . '/Uploads/Product/Large/' . $product_img_name;
                    $manager = new ImageManager(new Driver());
                    $image = $manager->read($spath);
                    $image = $image->scale(height: 300);
                    $image->save($dpath);

                    //Small Image
                    $dpath = public_path() . '/Uploads/Product/Small/' . $product_img_name;
                    $image = $manager->read($spath);
                    $image->coverDown(300, 300);
                    $image->save($dpath);
                }
            }
            session()->flash('success', 'Product Added !!');
            return response()->json([
                'status' => true,
                'message' => 'Product Added.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        }
    }

    //Get Product Data
    public function edit($id)
    {
        $data = [];
        $products = Product::find($id);

        if (empty($products)) {
            return redirect()->route('product.list')->with('error', 'Product not found !!');
        }

        //Get Product Image
        $productImages = ProductImage::where('product_id', $products->id)->get();
        $categories = Category::orderBy('name', 'ASC')->get();
        $subCategories = SubCategory::where('category_id', $products->category_id)->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        $data['products'] = $products;
        $data['categories'] = $categories;
        $data['subCategories'] = $subCategories;
        $data['productImages'] = $productImages;
        $data['brands'] = $brands;

        return view('admin.products.edit', $data);
    }

    //Update Product Data
    public function update($id, Request $request)
    {
        $product = Product::find($id);

        $rules =
            [
                'title' => 'required',
                'slug' => 'required|unique:products,slug,' . $product->id . ',id',
                'price' => 'required|numeric',
                'sku' => 'required|unique:products,sku,' . $product->id . ',id',
                'track_qty' => 'required|in:Yes,No',
                'category' => 'required|numeric',
                'is_featured' => 'required|in:Yes,No'

            ];
        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {

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
            session()->flash('success', 'Product Updated !!');
            return response()->json([
                'status' => true,
                'message' => 'Product Updated.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        }
    }

    public function delete($id)
    {
        $product = Product::find($id);
        if (empty($product)) {
            session()->flash('error', 'Product not found.');
            return response()->json([
                'status' => false,
                'notFound' => true
            ]);
        }

        $productImages = ProductImage::where('product_id', $id)->get();
        if (!empty($productImages)) {
            foreach ($productImages as $productImage) {
                File::delete(public_path('/Uploads/Product/Large/' . $productImage->image));
                File::delete(public_path('/Uploads/Product/Small/' . $productImage->image));
            }
            ProductImage::where('product_id', $id)->delete();
        }

        $product->delete();
        session()->flash('success', 'Product deleted !!');
        return response()->json([
            'status' => true,
            'message' => 'Product deleted.'
        ]);
    }
}

// comment for testing 