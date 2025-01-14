<?php

use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImageController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductImageController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => 'admin'], function(){

    Route::group(['middleware' => 'admin.guest'], function(){
        Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');
        Route::post('/authenticate', [AdminLoginController::class, 'authenticate'])->name('admin.authenticate');
    });

    Route::group(['middleware' => 'admin.auth'], function(){
        Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
        Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');

        //Category
        Route::controller(CategoryController::class)->group(function(){
            Route::get('/category/list', 'index')->name('category.list');
            Route::get('/category/getCategories', 'getCategories')->name('category.getCategories');
            Route::get('/category/create', 'create')->name('category.create');
            Route::get('/category/edit/{id}', 'edit')->name('category.edit');
            Route::post('/category/store', 'store')->name('category.store');
            Route::put('/category/update/{id}', 'update')->name('category.update');
            Route::delete('/category/delete/{id}', 'destroy')->name('category.delete');
            Route::get('/category/search', 'search')->name('category.search');
        });

        //Common Files
        Route::post('/upload-temp-image', [TempImageController::class, 'tempImage'])->name('temp-images.create');
        Route::get('/getSlug',[CategoryController::class, 'getSlug'])->name('getSlug');

        //Sub_category
        Route::controller(SubCategoryController::class)->group(function(){
            Route::get('/sub-category/create', 'create')->name('sub-category.create');
            Route::get('/sub-category/edit/{id}', 'edit')->name('sub-category.edit');
            Route::get('/sub-category', 'index')->name('sub-category.list');
            Route::post('/sub-category/store', 'store')->name('sub-category.store');
            Route::put('/sub-category/update/{id}', 'update')->name('sub-category.update');
            Route::delete('/sub-category/delete/{id}', 'delete')->name('sub-category.delete');
        });

        //brands
        Route::controller(BrandController::class)->group(function(){
            Route::get('/brand/add', 'create')->name('brand.create');
            Route::get('/brand/list', 'list')->name('brand.list');
            Route::post('/brand/store', 'store')->name('brand.store');
            Route::get('/brand/edit/{id}', 'edit')->name('brand.edit');
            Route::put('/brand/update/{id}', 'update')->name('brand.update');
            Route::delete('/brand/delete/{id}', 'delete')->name('brand.delete');
            Route::get('/brand/page', 'paginate')->name('brand.paginate');
        });

        //Products
        Route::controller(ProductController::class)->group(function(){
            Route::get('/product/list', 'list')->name('product.list');
            Route::get('/product/create', 'create')->name('product.create');
            Route::get('/product-subcategory', 'getSubCategory')->name('product-subcategory.getSubCategory');
            Route::post('/product/store', 'store')->name('product.store');
            Route::get('/product/edit/{id}', 'edit')->name('product.edit');
            Route::put('/product/{product}', 'update')->name('product.update');
            Route::delete('/product/{delete}', 'delete')->name('product.delete');
        });

        Route::controller(ProductImageController::class)->group(function(){
            Route::post('/product-images/update', 'update')->name('product-images.update');
            Route::delete('/product-images/delete', 'delete')->name('product-images.delete');

        });
    });
});








// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
