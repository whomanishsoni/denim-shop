@extends('layouts.app')

@section('title', 'Products - Denim Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Our Products</h1>
        <p class="text-lg text-gray-600">Discover our premium collection of denim clothing</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8" x-data="productsFilter()">
        <!-- Filters Sidebar -->
        <div class="lg:w-1/4">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h3 class="text-lg font-semibold mb-4">Filters</h3>
                
                <!-- Search -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input 
                        type="text" 
                        x-model="filters.search"
                        @input.debounce.300ms="loadProducts()"
                        placeholder="Search products..." 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Category Filter -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select 
                        x-model="filters.category"
                        @change="loadProducts()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Size Filter -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Size</label>
                    <div class="space-y-2">
                        @foreach($sizes as $size)
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    x-model="filters.sizes"
                                    value="{{ $size }}"
                                    @change="loadProducts()"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                >
                                <span class="ml-2 text-sm text-gray-700">{{ $size }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Color Filter -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                    <div class="space-y-2">
                        @foreach($colors as $color)
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    x-model="filters.colors"
                                    value="{{ $color }}"
                                    @change="loadProducts()"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                >
                                <span class="ml-2 text-sm text-gray-700">{{ $color }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Price Range -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input 
                            type="number" 
                            x-model="filters.min_price"
                            @input.debounce.500ms="loadProducts()"
                            placeholder="Min" 
                            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <input 
                            type="number" 
                            x-model="filters.max_price"
                            @input.debounce.500ms="loadProducts()"
                            placeholder="Max" 
                            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                </div>

                <!-- Sort By -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select 
                        x-model="filters.sort"
                        @change="loadProducts()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Latest</option>
                        <option value="name_asc">Name (A-Z)</option>
                        <option value="name_desc">Name (Z-A)</option>
                        <option value="price_asc">Price (Low to High)</option>
                        <option value="price_desc">Price (High to Low)</option>
                    </select>
                </div>

                <!-- Clear Filters -->
                <button 
                    @click="clearFilters()"
                    class="w-full bg-gray-200 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-300 transition-colors"
                >
                    Clear Filters
                </button>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="lg:w-3/4">
            <!-- Loading State -->
            <div x-show="loading" class="text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                <p class="mt-2 text-gray-600">Loading products...</p>
            </div>

            <!-- Products -->
            <div x-show="!loading">
                <!-- Results Info -->
                <div class="flex justify-between items-center mb-6">
                    <p class="text-gray-600">
                        Showing <span x-text="pagination.from || 0"></span> to <span x-text="pagination.to || 0"></span> of <span x-text="pagination.total || 0"></span> results
                    </p>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <template x-for="product in products" :key="product.id">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <a :href="`/products/${product.id}`">
                                <img :src="product.main_image || '/images/placeholder.jpg'" :alt="product.name" class="w-full h-64 object-cover">
                            </a>
                            <div class="p-4">
                                <span class="text-xs font-medium text-blue-600 bg-blue-100 px-2 py-1 rounded" x-text="product.category"></span>
                                <h3 class="font-medium text-gray-900 mt-2 mb-1">
                                    <a :href="`/products/${product.id}`" class="hover:text-blue-600" x-text="product.name"></a>
                                </h3>
                                <p class="text-lg font-bold text-gray-900 mb-2">₹<span x-text="parseFloat(product.price).toFixed(2)"></span></p>
                                <p class="text-gray-600 text-sm" x-text="product.description.length > 80 ? product.description.substring(0, 80) + '...' : product.description"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- No Results -->
                <div x-show="products.length === 0 && !loading" class="text-center py-8">
                    <p class="text-gray-600 text-lg">No products found matching your criteria.</p>
                </div>

                <!-- Pagination -->
                <div x-show="pagination.last_page > 1" class="flex justify-center items-center space-x-2 mt-8">
                    <button 
                        @click="changePage(pagination.current_page - 1)"
                        :disabled="pagination.current_page <= 1"
                        :class="pagination.current_page <= 1 ? 'opacity-50 cursor-not-allowed' : ''"
                        class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100"
                    >
                        Previous
                    </button>
                    
                    <template x-for="page in getPageNumbers()" :key="page">
                        <button 
                            @click="changePage(page)"
                            :class="page === pagination.current_page ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                            class="px-3 py-1 border border-gray-300 rounded"
                            x-text="page"
                        ></button>
                    </template>
                    
                    <button 
                        @click="changePage(pagination.current_page + 1)"
                        :disabled="pagination.current_page >= pagination.last_page"
                        :class="pagination.current_page >= pagination.last_page ? 'opacity-50 cursor-not-allowed' : ''"
                        class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100"
                    >
                        Next
                    </button>
                </div>
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

function productsFilter() {
    return {
        products: [],
        pagination: {},
        loading: true,
        filters: {
            search: '',
            category: '',
            sizes: [],
            colors: [],
            min_price: '',
            max_price: '',
            sort: '',
            page: 1,
            per_page: 12
        },

        init() {
            // Get URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('category')) {
                this.filters.category = urlParams.get('category');
            }
            if (urlParams.getAll('sizes[]')) {
                this.filters.sizes = urlParams.getAll('sizes[]');
            }
            if (urlParams.getAll('colors[]')) {
                this.filters.colors = urlParams.getAll('colors[]');
            }
            if (urlParams.get('min_price')) {
                this.filters.min_price = urlParams.get('min_price');
            }
            if (urlParams.get('max_price')) {
                this.filters.max_price = urlParams.get('max_price');
            }
            if (urlParams.get('sort')) {
                this.filters.sort = urlParams.get('sort');
            }
            
            this.loadProducts();
        },

        loadProducts() {
            this.loading = true;
            
            const params = new URLSearchParams();
            Object.keys(this.filters).forEach(key => {
                if (Array.isArray(this.filters[key])) {
                    this.filters[key].forEach(value => {
                        if (value) params.append(key, value);
                    });
                } else if (this.filters[key]) {
                    params.append(key, this.filters[key]);
                }
            });

            fetch(`/products/data?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    this.products = data.products;
                    this.pagination = data.pagination;
                    this.loading = false;
                })
                .catch(error => {
                    console.error('Error loading products:', error);
                    this.loading = false;
                });
        },

        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.filters.page = page;
                this.loadProducts();
            }
        },

        clearFilters() {
            this.filters = {
                search: '',
                category: '',
                sizes: [],
                colors: [],
                min_price: '',
                max_price: '',
                sort: '',
                page: 1,
                per_page: 12
            };
            this.loadProducts();
        },

        getPageNumbers() {
            const pages = [];
            const current = this.pagination.current_page;
            const last = this.pagination.last_page;
            
            for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
                pages.push(i);
            }
            
            return pages;
        }
    }
}
</script>
@endpush
