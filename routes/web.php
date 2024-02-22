<?php

use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImageController;
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

    });
});
