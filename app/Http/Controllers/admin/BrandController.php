<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    public function list(Request $request)
    {
        if ($request->ajax()) {
            $brand = Brand::latest()->get();
            return DataTables::of($brand)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = '<a href="javascript://" class="edit_brand" data-id="' . $data->id . '">
                            <svg class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                </path>
                            </svg>
                        </a>';
                    $btn .= '<a href="javascript://" data-record-id="' . $data->id . '"
                            data-record-title="' . $data->name . '" data-record-tag="brand" data-toggle="modal"
                            data-target="#confirm_delete" class="text-danger w-4 h-4 mr-1">
                            <svg wire:loading.remove.delay="" wire:target=""
                                class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
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
        return view('admin.brands.list');

        // $brand = Brand::latest()->paginate(10);
        // return view('admin.brands.list', compact('brand'));
    }

    // public function paginate(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $brand = Brand::paginate(10);
    //         return view('admin.brands.paginate', compact('brand'))->render();
    //     }
    // }

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
