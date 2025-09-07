<?php

namespace App\Http\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::latest()->take(8)->get();
        $categories = Product::getCategories();
        
        return view('home', compact('featuredProducts', 'categories'));
    }
}