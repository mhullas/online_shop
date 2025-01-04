<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{

    public function index()
    {
        $category = Category::orderBy('name', 'ASC')->get();
        $subCategory = SubCategory::select('sub_categories.*', 'categories.name as categoryName')
            ->latest('sub_categories.id')
            ->leftJoin('categories', 'categories.id', 'sub_categories.category_id');
        $subCategory = $subCategory->latest()->paginate(10);
        return view('admin.sub_category.list', compact('subCategory','category'));
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

    public function update($id,Request $request)
    {
        $subCategory = SubCategory::find($id);
        if (empty($subCategory)){
            session()->flash('error','Sub Category not found !!');
            return response()->json([
                'status' =>false,
                'message' => 'Sub Category not found.',
                'notFound' => true
            ]);
        }
        $validator = Validator::make($request->all(), [
            'up_name' => 'required',
            'up_slug' => 'required|unique:sub_categories,slug,'.$subCategory->id.',id',
            'up_category' => 'required',
            'up_status' => 'required'
        ]);

        if ($validator->passes()) {
            $subCategory->category_id = $request->up_category;
            $subCategory->name = $request->up_name;
            $subCategory->slug = $request->up_slug;
            $subCategory->status = $request->up_status;
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

    public function delete($id){
        $subCategory = SubCategory::find($id);
        if (empty($subCategory)){
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
