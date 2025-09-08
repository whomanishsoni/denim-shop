@extends('layouts.app')

@section('title', 'Home - Denim Store')

@section('content')
<!-- Hero Section -->
<div class="relative bg-gray-900 text-white">
    <div class="absolute inset-0">
        <img src="{{ \Illuminate\Support\Facades\Storage::url('hero/hero.jpeg') }}" alt="Denim Collection" class="w-full h-full object-cover opacity-50">
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Premium Denim Collection
            </h1>
            <p class="text-xl md:text-2xl mb-8 max-w-2xl mx-auto">
                Discover our carefully curated selection of high-quality denim pieces for every style and occasion.
            </p>
            <a href="{{ route('products.index') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg text-lg font-medium hover:bg-blue-700 transition-colors">
                Shop Now
            </a>
        </div>
    </div>
</div>

<!-- Categories Section -->
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Shop by Category</h2>
            <p class="text-lg text-gray-600">Find the perfect denim piece for your wardrobe</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
            @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category]) }}" class="group">
                    <div class="bg-gray-100 rounded-lg p-6 text-center hover:bg-gray-200 transition-colors">
                        <div class="text-2xl mb-3">
                            @switch($category)
                                @case('Jeans')
                                    👖
                                    @break
                                @case('Jackets')
                                    🧥
                                    @break
                                @case('Shirts')
                                    👔
                                    @break
                                @case('Shorts')
                                    🩳
                                    @break
                                @case('Vests')
                                    🦺
                                    @break
                            @endswitch
                        </div>
                        <h3 class="font-medium text-gray-900 group-hover:text-blue-600">{{ $category }}</h3>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>

<!-- Featured Products -->
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Featured Products</h2>
            <p class="text-lg text-gray-600">Check out our latest arrivals and bestsellers</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <a href="{{ route('products.show', $product) }}">
                        <img src="{{ $product->main_image }}" alt="{{ $product->name }}" class="w-full h-64 object-cover">
                    </a>
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-blue-600 bg-blue-100 px-2 py-1 rounded">
                                {{ $product->category }}
                            </span>
                        </div>
                        <h3 class="font-medium text-gray-900 mb-2">
                            <a href="{{ route('products.show', $product) }}" class="hover:text-blue-600">
                                {{ $product->name }}
                            </a>
                        </h3>
                        <div class="mb-2">
                            <span class="text-lg font-bold text-gray-900">
                                ₹{{ number_format($product->price, 2) }}
                            </span>
                        </div>
                        <p class="text-gray-600 text-sm">{{ Str::limit($product->description, 80) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="text-center mt-12">
            <a href="{{ route('products.index') }}" class="bg-gray-800 text-white px-8 py-3 rounded-lg hover:bg-gray-900 transition-colors">
                View All Products
            </a>
        </div>
    </div>
</div>

<!-- Newsletter Section -->
<div class="bg-blue-600 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">Stay Updated</h2>
        <p class="text-xl mb-8">Get notified about new products and exclusive deals</p>
        <div class="max-w-md mx-auto">
            <div class="flex">
                <input type="email" placeholder="Enter your email" class="flex-1 px-4 py-3 rounded-l-lg text-gray-900">
                <button class="bg-gray-800 px-6 py-3 rounded-r-lg hover:bg-gray-700 transition-colors">
                    Subscribe
                </button>
            </div>
        </div>
    </div>
</div>
@endsection