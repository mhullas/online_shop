<?php

use App\Models\Category;

function getCategories(){
    return Category::orderBy('name','ASC')
    ->where('status', 1)
    ->orderBy('id', 'DESC')
    ->where('showHome', 'Yes')
    ->with('subcategories')
    ->get();
}