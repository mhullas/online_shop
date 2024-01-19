<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(){
        $categories = Category::latest()->paginate(10);
        return view('admin.category.list',compact('categories'));
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories',
        ]);

        if ($validator->passes()) {

            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            //image Upload
            $img_id = $request->image_id;
            if (!empty($img_id)){
                $tempImage = new TempImage();
                $imgFind = $tempImage->find($img_id);
                $extArray = explode('.', $imgFind->name);
                $ext = last($extArray);
                $newImgName = $category->id.'.'.$ext;

                //copy or move
                $sPath = public_path().'/temp_images/'.$imgFind->name;
                $dPath = public_path().'/Uploads/Category/'.$newImgName;
                File::move($sPath, $dPath);

                $category->image = $newImgName;
                $category->save();
            }
            session()->flash('success','Category Added - flash !!');
            return response()->json([
                'status' => true,
                'message' => 'Category Added !!'
            ]);
            
            return redirect()->route('category.list')->with('success', 'Category Added !!');
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function getSlug(Request $request){
        $slug = '';
        if (!empty($request->name)){
            $slug = Str::slug($request->name);
        }
        return response()->json([
            'status' => true,
            'slug' => $slug
        ]);
    }
}
