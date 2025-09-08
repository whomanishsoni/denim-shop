@extends('layouts.app')

@section('title', $product->name . ' - Denim Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Product Images -->
        <div x-data="productGallery()" class="space-y-4">
            <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                <img :src="selectedImage" alt="{{ $product->name }}" class="w-full h-full object-cover">
            </div>
            
            @if($product->images && count($product->images) > 1)
                <div class="grid grid-cols-4 gap-2">
                    @foreach($product->images as $image)
                        <button 
                            @click="selectedImage = '{{ \Illuminate\Support\Facades\Storage::url($image) }}'"
                            :class="selectedImage === '{{ \Illuminate\Support\Facades\Storage::url($image) }}' ? 'ring-2 ring-blue-500' : ''"
                            class="aspect-square bg-gray-100 rounded-lg overflow-hidden hover:ring-2 hover:ring-gray-300"
                        >
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Product Details -->
        <div class="space-y-6">
            <div>
                <div class="flex items-center space-x-2 mb-2">
                    <span class="text-sm font-medium text-blue-600 bg-blue-100 px-2 py-1 rounded">
                        {{ $product->category }}
                    </span>
                    <span class="text-sm text-gray-500">{{ $product->stock }} in stock</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                <p class="text-3xl font-bold text-blue-600 mt-2">₹{{ number_format($product->price, 2) }}</p>
            </div>

            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                <p class="text-gray-600">{{ $product->description }}</p>
            </div>

            <!-- Size and Color Selection -->
            <div x-data="{ quantity: 1, selectedSize: '', selectedColor: '' }">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Size</label>
                    <select 
                        x-model="selectedSize"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Select Size</option>
                        @foreach($product->sizes ?? [] as $size)
                            <option value="{{ $size }}">{{ $size }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                    <select 
                        x-model="selectedColor"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Select Color</option>
                        @foreach($product->colors ?? [] as $color)
                            <option value="{{ $color }}">{{ $color }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center space-x-4 mb-4">
                    <label class="text-sm font-medium text-gray-700">Quantity:</label>
                    <div class="flex items-center space-x-2">
                        <button 
                            @click="quantity = Math.max(1, quantity - 1)"
                            class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300"
                        >
                            -
                        </button>
                        <span x-text="quantity" class="w-8 text-center"></span>
                        <button 
                            @click="quantity = Math.min({{ $product->stock }}, quantity + 1)"
                            class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300"
                        >
                            +
                        </button>
                    </div>
                </div>

                <button 
                    @click="addToCart({{ $product->id }}, quantity, selectedSize, selectedColor, $event)"
                    :disabled="{{ $product->stock == 0 ? 'true' : 'false' }} || !selectedSize || !selectedColor"
                    class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    @if($product->stock > 0)
                        Add to Cart
                    @else
                        Out of Stock
                    @endif
                </button>
            </div>

            <!-- Product Features -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Product Features</h3>
                <ul class="space-y-2 text-gray-600">
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Premium quality denim material
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Durable construction
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Machine washable
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Perfect fit and comfort
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function productGallery() {
    return {
        selectedImage: '{{ \Illuminate\Support\Facades\Storage::url($product->main_image) }}'
    }
}

function addToCart(productId, quantity, size, color, event) {
    if (!size || !color) {
        showMessage('Please select a size and color', 'error');
        return;
    }

    const button = event.target.closest('button');
    button.disabled = true;
    console.log('Adding to cart:', { product_id: productId, quantity: quantity, size: size, color: color });

    $.post('/cart/add', {
        product_id: productId,
        quantity: quantity,
        size: size,
        color: color
    })
    .done(function(data) {
        if (data.success) {
            showMessage(data.message, 'success');
            updateCartCount();
        }
    })
    .fail(function(xhr) {
        const response = xhr.responseJSON;
        showMessage(response.message || 'Failed to add product to cart', 'error');
    })
    .always(function() {
        button.disabled = false;
    });
}
</script>
@endpush