<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index');
    }

    public function data(Request $request)
    {
        $query = Product::query();

        // Search
        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('description', 'like', "%{$searchValue}%")
                  ->orWhere('category', 'like', "%{$searchValue}%");
            });
        }

        $totalRecords = Product::count();
        $filteredRecords = $query->count();

        // Ordering
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDir = $request->order[0]['dir'];
            $columns = ['id', 'name', 'category', 'price', 'stock', 'main_image'];
            if (isset($columns[$orderColumn])) {
                $query->orderBy($columns[$orderColumn], $orderDir);
            }
        }

        // Pagination
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        $products = $query->skip($start)->take($length)->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $products->map(function($product, $index) use ($start) {
                return [
                    'counter' => $start + $index + 1, // Counter starting from 1 for each page
                    'name' => $product->name,
                    'category' => $product->category,
                    'price' => '₹' . number_format($product->price, 2),
                    'stock' => $product->stock,
                    'main_image' => $product->main_image,
                    'actions' => view('admin.products.actions', compact('product'))->render()
                ];
            })
        ]);
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'required|in:' . implode(',', Product::getCategories()),
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();
        $imagePaths = [];

        // Handle file uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image && $image->isValid()) {
                    $path = $image->store('products', 'public');
                    $imagePaths[] = $path;
                }
            }
        }

        $data['images'] = $imagePaths;

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'required|in:' . implode(',', Product::getCategories()),
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'removed_images' => 'nullable|array',
        ]);

        $data = $request->all();
        $imagePaths = $product->images ?? [];

        // Remove images marked for deletion
        if ($request->has('removed_images')) {
            foreach ($request->input('removed_images', []) as $imageUrl) {
                $path = str_replace(Storage::url(''), '', $imageUrl); // Convert URL back to storage path
                if (in_array($path, $imagePaths)) {
                    Storage::disk('public')->delete($path);
                    $imagePaths = array_diff($imagePaths, [$path]);
                }
            }
            $imagePaths = array_values($imagePaths); // Reindex array
        }

        // Handle new file uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image && $image->isValid()) {
                    $path = $image->store('products', 'public');
                    $imagePaths[] = $path;
                }
            }
        }

        $data['images'] = $imagePaths;

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        // Delete associated images from storage
        if ($product->images) {
            foreach ($product->images as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $product->delete();

        return response()->json(['success' => 'Product deleted successfully!']);
    }
}