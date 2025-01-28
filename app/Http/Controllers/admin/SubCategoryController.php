<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SubCategoryController extends Controller
{

    public function index(Request $request)
    {
        $category = Category::orderBy('name', 'ASC')->get();
        if ($request->ajax()) {
            $subCategory = SubCategory::select('sub_categories.*', 'categories.name as categoryName')
                ->latest('sub_categories.id')
                ->leftJoin('categories', 'categories.id', 'sub_categories.category_id');
            // $ullas['category'] = $category;
            // $ullas['subCategory'] = $subCategory;
            return DataTables::of($subCategory)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = '<a href="javascript://" class="edit_sub_cat" data-id="' . $data->id . '">
                                            <svg class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path
                                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                                </path>
                                            </svg>
                                        </a>';
                    $btn .= '<a href="javascript://" data-record-id="' . $data->id . '"
                                            data-record-title="' . $data->name . '" data-record-tag="subcategory" data-toggle="modal"
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
                ->rawColumns(['action']) // Ensures HTML is rendered in the action column
                ->make(true);
        }
        return view('admin.sub_category.list', compact('category'));

        // $category = Category::orderBy('name', 'ASC')->get();
        // $subCategory = SubCategory::select('sub_categories.*', 'categories.name as categoryName')
        //     ->latest('sub_categories.id')
        //     ->leftJoin('categories', 'categories.id', 'sub_categories.category_id');
        // $subCategory = $subCategory->latest()->paginate(10);
        // return view('admin.sub_category.list', compact('subCategory','category'));
    }

    public function create()
    {

        $category = Category::orderBy('name', 'ASC')->get();

        return view('admin.sub_category.add', compact('category'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'category' => 'required',
            'status' => 'required'
        ]);

        if ($validator->passes()) {

            $subCategory = new SubCategory();
            $subCategory->category_id = $request->category;
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->showHome = $request->showHome;
            $subCategory->save();

            session()->flash('success', 'Sub Category Added !!');
            return response([
                'status' => true,
                'message' => 'Sub Category Added.'
            ]);
        } else {
            return response([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($id)
    {
        $subCategory = SubCategory::find($id);
        return response()->json([
            'status' => true,
            'getSubCategory' => $subCategory
        ]);
    }

    public function update($id, Request $request)
    {
        $subCategory = SubCategory::find($id);
        if (empty($subCategory)) {
            session()->flash('error', 'Sub Category not found !!');
            return response()->json([
                'status' => false,
                'message' => 'Sub Category not found.',
                'notFound' => true
            ]);
        }
        $validator = Validator::make($request->all(), [
            'up_name' => 'required',
            'up_slug' => 'required|unique:sub_categories,slug,' . $subCategory->id . ',id',
            'up_category' => 'required',
            'up_status' => 'required'
        ]);

        if ($validator->passes()) {
            $subCategory->category_id = $request->up_category;
            $subCategory->name = $request->up_name;
            $subCategory->slug = $request->up_slug;
            $subCategory->status = $request->up_status;
            $subCategory->showHome = $request->up_showHome;
            $subCategory->save();

            session()->flash('success', 'Sub Category Updated !!');
            return response([
                'status' => true,
                'message' => 'Sub Category Updated.'
            ]);
        } else {
            return response([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function delete($id)
    {
        $subCategory = SubCategory::find($id);
        if (empty($subCategory)) {
            session()->flash('error', 'Sub Category not found !!');
            return response()->json([
                'status' => false,
                'message' => 'Sub Category not found.'
            ]);
        }
        $subCategory->delete();
        session()->flash('success', 'Sub Category Deleted !!');
        return response()->json([
            'status' => true,
            'message' => 'Sub Category Deleted.'
        ]);
    }
}
