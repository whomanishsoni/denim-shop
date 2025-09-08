<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Product::getCategories();
        $sizes = Product::getSizes();
        $colors = Product::getColors();
        return view('products.index', compact('categories', 'sizes', 'colors'));
    }

    public function data(Request $request)
    {
        $query = Product::query();

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
        }

        // Category filter
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Size filter
        if ($request->has('size') && $request->size) {
            $query->whereJsonContains('sizes', $request->size);
        }

        // Color filter
        if ($request->has('color') && $request->color) {
            $query->whereJsonContains('colors', $request->color);
        }

        // Price range filter
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Ordering
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'name_asc':
                    $query->orderBy('name');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'price_asc':
                    $query->orderBy('price');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        }

        $totalRecords = Product::count();
        $filteredRecords = $query->count();

        // Pagination
        $page = $request->page ?? 1;
        $perPage = $request->per_page ?? 12;
        $products = $query->paginate($perPage, ['*'], 'page', $page);

        // Transform main_image to full storage URL
        $products->getCollection()->transform(function ($product) {
            $product->main_image = $product->main_image ? Storage::url($product->main_image) : '/images/placeholder.jpg';
            return $product;
        });

        return response()->json([
            'products' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]
        ]);
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }
}