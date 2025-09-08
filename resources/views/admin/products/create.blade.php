@extends('layouts.admin')

@section('title', 'Add Product')
@section('header', 'Add Product')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" x-data="productForm()">
        @csrf
        
        <div class="bg-white rounded-lg shadow-md p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name') }}"
                    required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                >
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea 
                    name="description" 
                    rows="4"
                    required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                    <input 
                        type="number" 
                        name="price" 
                        step="0.01"
                        min="0"
                        value="{{ old('price') }}"
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('price') border-red-500 @enderror"
                    >
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                    <input 
                        type="number" 
                        name="stock" 
                        min="0"
                        value="{{ old('stock', 0) }}"
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('stock') border-red-500 @enderror"
                    >
                    @error('stock')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select 
                    name="category" 
                    required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('category') border-red-500 @enderror"
                >
                    <option value="">Select Category</option>
                    @foreach(\App\Models\Product::getCategories() as $category)
                        <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
                @error('category')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sizes</label>
                <div class="flex flex-wrap gap-4 @error('sizes') border border-red-500 rounded-md p-2 @enderror">
                    @foreach(\App\Models\Product::getSizes() as $size)
                        <label class="inline-flex items-center">
                            <input 
                                type="checkbox" 
                                name="sizes[]" 
                                value="{{ $size }}"
                                {{ in_array($size, old('sizes', [])) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">{{ $size }}</span>
                        </label>
                    @endforeach
                </div>
                @error('sizes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Colors</label>
                <div class="flex flex-wrap gap-4 @error('colors') border border-red-500 rounded-md p-2 @enderror">
                    @foreach(\App\Models\Product::getColors() as $color)
                        <label class="inline-flex items-center">
                            <input 
                                type="checkbox" 
                                name="colors[]" 
                                value="{{ $color }}"
                                {{ in_array($color, old('colors', [])) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">{{ $color }}</span>
                        </label>
                    @endforeach
                </div>
                @error('colors')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Images (JPEG, PNG, JPG only)</label>
                <div class="space-y-2">
                    <template x-for="(image, index) in images" :key="index">
                        <div class="flex items-center space-x-2">
                            <div class="flex-1">
                                <input 
                                    type="file" 
                                    :name="`images[${index}]`"
                                    accept="image/jpeg,image/png,image/jpg"
                                    @change="previewImage($event, index)"
                                    x-ref="fileInput${index}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                <div x-show="imagePreviews[index]" class="mt-2">
                                    <img :src="imagePreviews[index]" class="w-32 h-32 object-cover rounded-md" alt="Image Preview">
                                </div>
                            </div>
                            <button 
                                type="button"
                                @click="removeImage(index)"
                                x-show="images.length > 1"
                                class="text-red-600 hover:text-red-800"
                            >
                                Remove
                            </button>
                        </div>
                    </template>
                    <button 
                        type="button"
                        @click="addImage()"
                        class="text-blue-600 hover:text-blue-800 text-sm"
                    >
                        + Add Image
                    </button>
                </div>
                @error('images.*')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center space-x-4">
                <button 
                    type="submit" 
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Create Product
                </button>
                <a 
                    href="{{ route('admin.products.index') }}" 
                    class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors"
                >
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function productForm() {
    return {
        images: [''],
        imagePreviews: [],

        addImage() {
            this.images.push('');
            this.imagePreviews.push(null);
        },

        removeImage(index) {
            this.images.splice(index, 1);
            this.imagePreviews.splice(index, 1);
            this.$refs['fileInput' + index]?.setAttribute('value', '');
        },

        previewImage(event, index) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreviews[index] = e.target.result;
                    this.$forceUpdate();
                };
                reader.readAsDataURL(file);
            } else {
                this.imagePreviews[index] = null;
            }
        }
    }
}
</script>
@endpush