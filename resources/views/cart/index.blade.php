@extends('layouts.app')

@section('title', 'Shopping Cart - Denim Store')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="cart()">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

    <div x-show="cartItems.length === 0" class="text-center py-12">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6" />
        </svg>
        <h2 class="text-xl font-medium text-gray-900 mb-2">Your cart is empty</h2>
        <p class="text-gray-600 mb-6">Looks like you haven't added any items to your cart yet.</p>
        <a href="{{ route('products.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
            Continue Shopping
        </a>
    </div>

    <div x-show="cartItems.length > 0">
        <!-- Cart Items -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <template x-for="item in cartItems" :key="item.id + '-' + item.size + '-' + item.color">
                <div class="flex items-center p-6 border-b border-gray-200 last:border-b-0">
                    <img :src="item.image" :alt="item.name" class="w-20 h-20 object-cover rounded">
                    
                    <div class="flex-1 ml-4">
                        <h3 class="text-lg font-medium text-gray-900" x-text="item.name"></h3>
                        <p class="text-gray-600">₹<span x-text="parseFloat(item.price).toFixed(2)"></span> each</p>
                        <p class="text-gray-600 text-sm">Size: <span x-text="item.size"></span>, Color: <span x-text="item.color"></span></p>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <button 
                            @click="updateQuantity(item.id, item.quantity - 1, item.size, item.color)"
                            class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300"
                        >
                            -
                        </button>
                        <span class="w-8 text-center" x-text="item.quantity"></span>
                        <button 
                            @click="updateQuantity(item.id, item.quantity + 1, item.size, item.color)"
                            class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300"
                        >
                            +
                        </button>
                    </div>
                    
                    <div class="ml-6 text-right">
                        <p class="text-lg font-medium text-gray-900">
                            ₹<span x-text="(parseFloat(item.price) * item.quantity).toFixed(2)"></span>
                        </p>
                        <button 
                            @click="removeItem(item.id, item.size, item.color)"
                            class="text-red-600 hover:text-red-800 text-sm"
                        >
                            Remove
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Cart Summary -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center border-t pt-4">
                <span class="text-xl font-semibold text-gray-900">Total:</span>
                <span class="text-2xl font-bold text-gray-900">₹<span x-text="cartTotal.toFixed(2)"></span></span>
            </div>
            
            <div class="mt-6 flex flex-col sm:flex-row gap-4">
                <a 
                    href="{{ route('products.index') }}" 
                    class="flex-1 bg-gray-200 text-gray-800 py-3 px-6 rounded-lg text-center hover:bg-gray-300 transition-colors"
                >
                    Continue Shopping
                </a>
                @auth
                    <a 
                        href="{{ route('checkout') }}" 
                        class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg text-center hover:bg-blue-700 transition-colors"
                    >
                        Checkout
                    </a>
                @else
                    <a 
                        href="{{ route('login') }}" 
                        class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg text-center hover:bg-blue-700 transition-colors"
                    >
                        Login to Checkout
                    </a>
                @endauth
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

function cart() {
    return {
        cartItems: @json($cartItems),
        cartTotal: 0,

        init() {
            console.log('Cart items loaded:', this.cartItems);
            this.calculateTotal();
        },

        calculateTotal() {
            this.cartTotal = this.cartItems.reduce((total, item) => {
                return total + (parseFloat(item.price) * item.quantity);
            }, 0);
            console.log('Cart total calculated:', this.cartTotal);
        },

        updateQuantity(productId, quantity, size, color) {
            console.log('Updating quantity:', { productId, quantity, size, color });
            if (quantity <= 0) {
                this.removeItem(productId, size, color);
                return;
            }

            $.ajax({
                url: '/cart/update',
                method: 'PUT',
                data: {
                    product_id: productId,
                    quantity: quantity,
                    size: size,
                    color: color
                },
                success: (data) => {
                    if (data.success) {
                        const item = this.cartItems.find(item => item.id == productId && item.size == size && item.color == color);
                        if (item) {
                            item.quantity = quantity;
                        }
                        this.calculateTotal();
                        updateCartCount();
                        showMessage(data.message, 'success');
                    }
                },
                error: (xhr) => {
                    const response = xhr.responseJSON;
                    showMessage(response.message || 'Failed to update cart', 'error');
                }
            });
        },

        removeItem(productId, size, color) {
            console.log('Removing item:', { productId, size, color });
            $.ajax({
                url: '/cart/remove',
                method: 'DELETE',
                data: {
                    product_id: productId,
                    size: size,
                    color: color
                },
                success: (data) => {
                    if (data.success) {
                        this.cartItems = this.cartItems.filter(item => !(item.id == productId && item.size == size && item.color == color));
                        this.calculateTotal();
                        updateCartCount();
                        showMessage(data.message, 'success');
                    }
                },
                error: (xhr) => {
                    const response = xhr.responseJSON;
                    showMessage(response.message || 'Failed to remove item from cart', 'error');
                }
            });
        }
    }
}
</script>
@endpush