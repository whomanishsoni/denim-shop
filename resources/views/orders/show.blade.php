@extends('layouts.app')

@section('title', 'Order #' . $order->id . ' - Denim Store')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-700">← Back to Orders</a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Order Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->id }}</h1>
                    <p class="text-gray-600">Placed on {{ $order->created_at->format('F j, Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($order->total, 2) }}</p>
                    <span class="inline-block px-3 py-1 text-sm font-medium rounded-full {{ $order->status_badge }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h2>
            <div class="space-y-4">
                @foreach($order->orderItems as $item)
                    <div class="flex items-center space-x-4">
                        <img src="{{ $item->product->main_image }}" alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900">{{ $item->product->name }}</h3>
                            <p class="text-gray-600">{{ $item->product->category }}</p>
                            <p class="text-gray-600">Quantity: {{ $item->quantity }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">${{ number_format($item->price, 2) }} each</p>
                            <p class="text-gray-600">${{ number_format($item->price * $item->quantity, 2) }} total</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Shipping Address</h2>
            <div class="text-gray-600">
                <p>{{ $order->shipping_address['address'] }}</p>
                <p>{{ $order->shipping_address['city'] }}, {{ $order->shipping_address['postal_code'] }}</p>
                <p>Phone: {{ $order->shipping_address['phone'] }}</p>
            </div>
        </div>
    </div>
</div>
@endsection