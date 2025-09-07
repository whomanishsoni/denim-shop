@extends('layouts.app')

@section('title', 'Checkout - Denim Store')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="checkout()">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Order Form -->
        <div>
            <form @submit.prevent="submitOrder()" class="space-y-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Shipping Information</h2>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <input 
                                type="text" 
                                x-model="form.address"
                                required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter Your Address"
                            >
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                <input 
                                    type="text" 
                                    x-model="form.city"
                                    required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Enter Your City"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                                <input 
                                    type="text" 
                                    x-model="form.postal_code"
                                    required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Enter 6 Digit Pincode"
                                >
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                            <input 
                                type="tel" 
                                x-model="form.phone"
                                required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter 10-digit phone number"
                            >
                        </div>
                    </div>
                </div>

                <button 
                    type="submit"
                    :disabled="submitting"
                    class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span x-show="!submitting">Place Order</span>
                    <span x-show="submitting">Processing...</span>
                </button>
            </form>
        </div>

        <!-- Order Summary -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>
                
                <div class="space-y-4 mb-6">
                    @foreach($cart as $item)
                        <div class="flex items-center space-x-4">
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-16 h-16 object-cover rounded">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900">{{ $item['name'] }}</h3>
                                <p class="text-gray-600">Quantity: {{ $item['quantity'] }}</p>
                            </div>
                            <span class="font-medium text-gray-900">
                                ${{ number_format($item['price'] * $item['quantity'], 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>
                
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium text-gray-900">
                            ${{ number_format(array_sum(array_map(function($item) { return $item['price'] * $item['quantity']; }, $cart)), 2) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Shipping:</span>
                        <span class="font-medium text-green-600">Free</span>
                    </div>
                    <div class="flex justify-between items-center text-lg font-semibold border-t pt-2">
                        <span>Total:</span>
                        <span>
                            ${{ number_format(array_sum(array_map(function($item) { return $item['price'] * $item['quantity']; }, $cart)), 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function checkout() {
    return {
        form: {
            address: '',
            city: '',
            postal_code: '',
            phone: ''
        },
        submitting: false,

        submitOrder() {
            this.submitting = true;
            
            $.post('/orders', this.form)
                .done((data) => {
                    if (data.success) {
                        showMessage(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = `/orders/${data.order_id}`;
                        }, 1000);
                    }
                })
                .fail((xhr) => {
                    const response = xhr.responseJSON;
                    showMessage(response.message || 'Failed to place order', 'error');
                })
                .always(() => {
                    this.submitting = false;
                });
        }
    }
}
</script>
@endpush