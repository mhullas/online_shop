<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function list()
    {
        $brand = Brand::paginate(10);
        return view('admin.brands.list', compact('brand'));
    }

    public function paginate(Request $request)
    {
        if ($request->ajax()) {
            $brand = Brand::paginate(10);
            return view('admin.brands.paginate', compact('brand'))->render();
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands'
        ]);

        if ($validator->passes()) {

            $brand = new Brand();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;

            $brand->save();

            session()->flash('success', 'New Brand Added !!');
            return response()->json([
                'status' => true,
                'message' => 'Brand Added !!'
            ]);
            return redirect()->route('brand.list')->with('success', 'New Brand added !!');
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($id)
    {
        $brand = Brand::find($id);
        return response()->json([
            'status' => true,
            'getBrand' => $brand
        ]);
    }

    public function update($id, Request $request)
    {

        $brand = Brand::find($id);
        if (empty($id)) {
            session()->flash('error', 'Brand not found !!');
            return response()->json([
                'status' => false,
                'message' => 'Brand not found.',
                'notFound' => true
            ]);
        }

        $validator = Validator::make($request->all(), [
            'up_name' => 'required',
            'up_slug' => 'required|unique:brands,slug,' . $brand->id . ',id',
        ]);

        if ($validator->passes()) {
            $brand->name = $request->up_name;
            $brand->slug = $request->up_slug;
            $brand->status = $request->up_status;
            $brand->save();

            session()->flash('success', 'Brand Updated !!');
            return response()->json([
                'status' => true,
                'message' => 'Brand Updated.'
            ]);
            return redirect()->route('brand.list')->with('success', 'Brand Updated !!');
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }


    public function delete($id)
    {
        $brand = Brand::find($id);

        if (empty($brand)) {
            session()->flash('error', 'Brand not found !!');
            return response()->json([
                'status' => false,
                'message' => 'Brand not found.',
                'notFound' => true
            ]);
        }
        $brand->delete();
        session()->flash('success', 'Brand deleted !!');
        return response()->json([
            'status' => true,
            'message' => 'Brand deleted.'
        ]);
        return redirect()->route('brand.list')->with('success', 'Brand Deleted.');
    }
}
