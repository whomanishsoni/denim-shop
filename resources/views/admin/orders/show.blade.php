@extends('layouts.admin')

@section('title', 'Order #' . $order->id)
@section('header', 'Order Details')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-700">← Back to Orders</a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Order Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->id }}</h1>
                    <p class="text-gray-600">Placed on {{ $order->created_at->format('F j, Y g:i A') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-gray-900">₹{{ number_format($order->total, 2) }}</p>
                    <span class="inline-block px-3 py-1 text-sm font-medium rounded-full {{ $order->status_badge }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-medium text-gray-900 mb-2">Customer Details</h3>
                    <p class="text-gray-600">Name: {{ $order->user->name }}</p>
                    <p class="text-gray-600">Email: {{ $order->user->email }}</p>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900 mb-2">Shipping Address</h3>
                    <div class="text-gray-600">
                        <p>{{ $order->shipping_address['address'] }}</p>
                        <p>{{ $order->shipping_address['city'] }}, {{ $order->shipping_address['postal_code'] }}</p>
                        <p>Phone: {{ $order->shipping_address['phone'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Product</th>
                            <th class="text-left py-2">Price</th>
                            <th class="text-left py-2">Quantity</th>
                            <th class="text-left py-2">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                            <tr class="border-b">
                                <td class="py-4">
                                    <div class="flex items-center space-x-3">
                                        <img src="{{ $item->product->main_image }}" alt="{{ $item->product->name }}" class="w-12 h-12 object-cover rounded">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $item->product->name }}</p>
                                            <p class="text-sm text-gray-600">{{ $item->product->category }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 text-gray-600">₹{{ number_format($item->price, 2) }}</td>
                                <td class="py-4 text-gray-600">{{ $item->quantity }}</td>
                                <td class="py-4 font-medium text-gray-900">₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="py-4 text-right font-semibold text-gray-900">Total:</td>
                            <td class="py-4 font-bold text-gray-900">₹{{ number_format($order->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Actions -->
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>
            <button 
                onclick="updateOrderStatus({{ $order->id }}, '{{ $order->status }}')"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
            >
                Update Status
            </button>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div id="status-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Update Order Status</h3>
            <form id="status-form">
                <input type="hidden" id="order-id" name="order_id" value="{{ $order->id }}">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="order-status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    </select>
                </div>
                <div class="flex items-center space-x-4">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Update Status
                    </button>
                    <button type="button" onclick="closeStatusModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Status form submission
$('#status-form').on('submit', function(e) {
    e.preventDefault();
    const orderId = $('#order-id').val();
    const status = $('#order-status').val();

    $.ajax({
        url: `/admin/orders/${orderId}/status`,
        method: 'PUT',
        data: { status: status },
        success: function(data) {
            showMessage(data.success, 'success');
            closeStatusModal();
            setTimeout(() => {
                location.reload();
            }, 1000);
        },
        error: function(xhr) {
            showMessage('Failed to update order status', 'error');
        }
    });
});

function updateOrderStatus(orderId, currentStatus) {
    $('#order-id').val(orderId);
    $('#order-status').val(currentStatus);
    $('#status-modal').removeClass('hidden');
}

function closeStatusModal() {
    $('#status-modal').addClass('hidden');
}
</script>
@endpush