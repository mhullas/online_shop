<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('id', 'DESC')
            ->where('is_featured', 'Yes')
            ->where('status', 1)
            ->take(8)
            ->get();
        $data['featuredProducts'] = $products;

        $latestProduct = Product::orderBy('id', 'DESC')
            ->where('status', 1)
            ->take(8)
            ->get();
        $data['latestProducts'] = $latestProduct;


        return view('front.home', $data);
    }
}
