@extends('layouts.admin')

@section('title', 'View Product')
@section('header', 'Product Details')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:text-blue-700">← Back to Products</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Product Images -->
        <div x-data="{ selectedImage: '{{ $product->main_image }}' }" class="space-y-4">
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
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-blue-600 bg-blue-100 px-2 py-1 rounded">
                            {{ $product->category }}
                        </span>
                        <span class="text-sm text-gray-500">ID: #{{ $product->id }}</span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                    <p class="text-2xl font-bold text-green-600 mt-2">₹{{ number_format($product->price, 2) }}</p>
                </div>

                <div class="border-t pt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Stock Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-gray-600">Available Stock:</span>
                            <span class="font-medium {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $product->stock }} units
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                    <p class="text-gray-600">{{ $product->description }}</p>
                </div>

                <div class="border-t pt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Timestamps</h3>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div>
                            <span class="font-medium">Created:</span>
                            {{ $product->created_at->format('M j, Y g:i A') }}
                        </div>
                        <div>
                            <span class="font-medium">Last Updated:</span>
                            {{ $product->updated_at->format('M j, Y g:i A') }}
                        </div>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <div class="flex space-x-3">
                        <a 
                            href="{{ route('admin.products.edit', $product) }}" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            Edit Product
                        </a>
                        <button 
                            onclick="deleteProduct({{ $product->id }})"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors"
                        >
                            Delete Product
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        $.ajax({
            url: '/admin/products/' + id,
            method: 'DELETE',
            success: function(data) {
                showMessage(data.success, 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("admin.products.index") }}';
                }, 1000);
            },
            error: function(xhr) {
                showMessage('Failed to delete product', 'error');
            }
        });
    }
}
</script>
@endpush