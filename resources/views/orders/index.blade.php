@extends('layouts.app')

@section('title', 'My Orders - Denim Store')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="orders()">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">My Orders</h1>

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-2 text-gray-600">Loading orders...</p>
    </div>

    <!-- Orders -->
    <div x-show="!loading">
        <!-- No Orders -->
        <div x-show="orders.length === 0" class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h2 class="text-xl font-medium text-gray-900 mb-2">No orders yet</h2>
            <p class="text-gray-600 mb-6">You haven't placed any orders yet.</p>
            <a href="{{ route('products.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                Start Shopping
            </a>
        </div>

        <!-- Orders List -->
        <div x-show="orders.length > 0" class="space-y-6">
            <template x-for="order in orders" :key="order.id">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Order #<span x-text="order.id"></span></h3>
                                <p class="text-gray-600">Placed on <span x-text="order.created_at"></span></p>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold text-gray-900">₹<span x-text="parseFloat(order.total).toFixed(2)"></span></p>
                                <span 
                                    class="inline-block px-3 py-1 text-xs font-medium rounded-full"
                                    :class="order.status_badge"
                                    x-text="order.status.charAt(0).toUpperCase() + order.status.slice(1)"
                                ></span>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600"><span x-text="order.items_count"></span> items</span>
                            <a 
                                :href="`/orders/${order.id}`"
                                class="text-blue-600 hover:text-blue-700 font-medium"
                            >
                                View Details →
                            </a>
                        </div>
                    </div>
                </div>
            </template>
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
@endsection

@push('scripts')
<script>
function orders() {
    return {
        orders: [],
        pagination: {},
        loading: true,

        init() {
            this.loadOrders();
        },

        loadOrders(page = 1) {
            this.loading = true;
            
            fetch(`/orders/data?page=${page}`)
                .then(response => response.json())
                .then(data => {
                    this.orders = data.orders;
                    this.pagination = data.pagination;
                    this.loading = false;
                })
                .catch(error => {
                    console.error('Error loading orders:', error);
                    this.loading = false;
                });
        },

        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.loadOrders(page);
            }
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