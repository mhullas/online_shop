<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\DataTables\CategoryDataTable;


class CategoryController extends Controller
{
    // public function index(CategoryDataTable $dataTable)
    // {
    //     return $dataTable->render('admin.category.list');
    // }
    public function index(Request $request)
    {
        // return DataTables::of(Category::query())->make(true);

        $categories = Category::latest()->get();
        return view('admin.category.list', compact('categories'));
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
            $category->image_id = $request->image_id;
            $category->save();

            //image Upload
            $img_id = $request->image_id;
            if (!empty($img_id)) {
                $tempImage = new TempImage();
                $imgFind = $tempImage->find($img_id);
                $extArray = explode('.', $imgFind->name);
                $ext = last($extArray);
                $newImgName = $category->id . '.' . $ext;

                //copy or move
                $sPath = public_path() . '/temp_images/' . $imgFind->name;
                $dPath = public_path() . '/Uploads/Category/' . $newImgName;

                $manager = new ImageManager(new Driver());
                $image = $manager->read($sPath);
                $image = $image->resize(560, 400);
                $image->toJpeg(300)->save(public_path() . '/Uploads/Category/thumb/' . $newImgName);

                $category->image = $newImgName;
                $category->save();

                // //delete TempImage
                // $imgId = $category->image_id;
                // if (!empty($imgId)) {
                //     $tempImage = TempImage::find($imgId);
                //     $tempImage->delete();
                //     // $sPath = public_path() . '/temp_images/' . $tempImage->name;
                //     // File::delete($sPath);
                // }
            }
            session()->flash('success', 'Category Added !!');
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

    public function edit($id)
    {
        $category = Category::find($id);
        return response()->json([
            'status' => true,
            'getCategory' => $category,
        ]);
    }

    public function update($categoryId, Request $request)
    {

        $category = Category::find($categoryId);
        if (empty($category)) {
            session()->flash('error', 'Category not Found !!');
            return response()->json([
                'status' => false,
                'message' => 'Category Not Found !!',
                'notfound' => true
            ]);
        }

        $validator = Validator::make($request->all(), [
            'up_name' => 'required',
            'up_slug' => 'required|unique:categories,slug,' . $category->id . ',id',
        ]);

        if ($validator->passes()) {
            $category->name = $request->up_name;
            $category->slug = $request->up_slug;
            $category->status = $request->up_status;
            $category->image_id = $request->up_imageId;
            $category->save();

            //image Upload
            $img_id = $request->up_imageId;
            if (!empty($img_id)) {
                $tempImage = new TempImage();
                $imgFind = $tempImage->find($img_id);
                $extArray = explode('.', $imgFind->name);
                $ext = last($extArray);
                $newImgName = $category->id . '.' . $ext;

                //copy or move
                $sPath = public_path() . '/temp_images/' . $imgFind->name;
                $dPath = public_path() . '/Uploads/Category/' . $newImgName;
                $manager = new ImageManager(new Driver());
                $image = $manager->read($sPath);
                $image = $image->resize(560, 400);
                $image->toJpeg(300)->save(public_path() . '/Uploads/Category/thumb/' . $newImgName);
                File::move($sPath, $dPath);

                $category->image = $newImgName;
                $category->save();

                // //Delete TempImage
                // $imgId = $category->image_id;
                // if (!empty($imgId)) {
                //     $tempImage = TempImage::find($imgId);
                //     $tempImage->delete();
                //     $sPath = public_path() . '/temp_images/' . $tempImage->name;
                //     File::delete($sPath);
                // }
            }
            session()->flash('success', 'Category Updated !!');
            return response()->json([
                'status' => true,
                'message' => 'Category Added !!'
            ]);

            return redirect()->route('category.list')->with('success', 'Category Updated !!');
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function destroy($id, Request $request)
    {
        $category = Category::find($id);
        if (empty($category)) {
            session()->flash('error', 'Category not Found !!');
            return response()->json([
                'status' => true,
                'message' => 'Category not Found.'
            ]);
        }elseif ($category && $category->subcategories()->count() == 0) {
            $category->delete();
            $sPath = public_path() . '/Uploads/Category/thumb/' . $category->image;
            $dPath = public_path() . '/Uploads/Category/' . $category->image;
            File::delete($sPath, $dPath);
            session()->flash('success', 'Category Deleted !!');
            return response()->json([
                'status' => true,
                'message' => 'Category Deleted.',
                'getCategory' => $category
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Category has Sub-Categories.',
                'getCategory' => $category
            ]);
        }
    }

    public function getSlug(Request $request)
    {
        $slug = '';
        if (!empty($request->title)) {
            $slug = Str::slug($request->title);
        }
        return response()->json([
            'status' => true,
            'slug' => $slug
        ]);
    }

    public function search(Request $request)
    {
        $search = Category::where('name', 'like', '%' . $request->search . '%')
            ->orWhere('slug', 'like', '%' . $request->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        if ($search->count() >= 1) {
            return view('admin.category.paginate', compact('search'));
        } else {
            return response()->json([
                'status' => 'nothing_found',
            ]);
        }
    }
}
