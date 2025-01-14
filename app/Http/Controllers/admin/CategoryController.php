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
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    // public function index(CategoryDataTable $dataTable)
    // {
    //     return $dataTable->render('admin.category.list');
    // }
    public function index(Request $request)
    {

        return view('admin.category.list');

        // $categories = Category::latest()->get();
        // return view('admin.category.list', compact('categories'));
    }

    public function getCategories()
    {
        $categories = Category::latest()->get();

        return DataTables::of($categories)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                // Debugging
                // Uncomment to check structure

                $btn = '<a href="javascript://" class="edit_cat" data-id="' . $data->id . '">
                        <svg class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                            </path>
                        </svg>
                    </a>';
                $btn .= '<a href="javascript://" data-record-id="' . $data->id . '"
                        data-record-title="' . $data->name . '" data-toggle="modal"
                        data-target="#confirm_delete" class="text-danger w-4 h-4 mr-1">
                        <svg wire:loading.remove.delay="" wire:target=""
                            class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path ath fill-rule="evenodd"
                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                clip-rule="evenodd">
                            </path>
                        </svg>
                    </a>';
                    dd($data->id);
                return $btn;
            })
            ->rawColumns(['action']) // Ensures HTML is rendered in the action column
            ->make(true);
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
                $sPath = public_path() . '/temp_images/thumb/' . $imgFind->name;
                $manager = new ImageManager(new Driver());
                $image = $manager->read($sPath);
                $image = $image->resize(560, 400);
                $image->toJpeg(300)->save(public_path() . '/Uploads/Category/thumb/' . $newImgName);

                $category->image = $newImgName;
                $category->save();

                //delete TempImage
                $imgId = $category->image_id;
                if (!empty($imgId)) {
                    $tempImage = TempImage::find($imgId);
                    $tempImage->delete();
                    $sPath = public_path() . '/temp_images/thumb/' . $tempImage->name;
                    File::delete($sPath);
                }
            } else {
                return redirect()->route('category.list');
            }
            session()->flash('success', 'Category Added !!');
            return response()->json([
                'status' => true,
                'message' => 'Category Added !!'
            ]);
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
            $oldImg_id = $request->getImgId;
            $categoryImg = $category->image_id;
            
            if ($img_id == $oldImg_id) {
                return response()->json([
                    'notUpdate' => true
                ]);
            } elseif (!empty($img_id)){
                $tempImage = new TempImage();
                $imgFind = $tempImage->find($img_id);
                $extArray = explode('.', $imgFind->name);
                $ext = last($extArray);
                $newImgName = $category->id . '.' . $ext;

                //copy or move
                $sPath = public_path() . '/temp_images/thumb/' . $imgFind->name;
                $manager = new ImageManager(new Driver());
                $image = $manager->read($sPath);
                $image = $image->resize(560, 400);
                $image->toJpeg(300)->save(public_path() . '/Uploads/Category/thumb/' . $newImgName);

                $category->image = $newImgName;
                $category->save();

                //Delete TempImage
                $imgId = $category->image_id;
                if (!empty($imgId)) {
                    $tempImage = TempImage::find($imgId);
                    $tempImage->delete();
                    $sPath = public_path() . '/temp_images/thumb/' . $tempImage->name;
                    File::delete($sPath);
                }
            }
            session()->flash('success', 'Category Updated !!');
            return response()->json([
                'status' => true,
                'message' => 'Category Updated.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if (empty($category)) {
            session()->flash('error', 'Category not Found !!');
            return response()->json([
                'status' => true,
                'message' => 'Category not Found.'
            ]);
        } elseif ($category && $category->subcategories()->count() == 0) {
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
