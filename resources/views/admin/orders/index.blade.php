@extends('layouts.admin')

@section('title', 'Orders Management')
@section('header', 'Orders')

@section('content')
<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">All Orders</h2>
    </div>
    
    <div class="p-6">
        <table id="orders-table" class="w-full">
            <thead>
                <tr>
                    <th class="text-left">Order ID</th>
                    <th class="text-left">Customer</th>
                    <th class="text-left">Total</th>
                    <th class="text-left">Status</th>
                    <th class="text-left">Date</th>
                    <th class="text-left">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Status Update Modal -->
<div id="status-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Update Order Status</h3>
            <form id="status-form">
                <input type="hidden" id="order-id" name="order_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="order-status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="pending">Pending</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
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
$(document).ready(function() {
    $('#orders-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.orders.data") }}',
            type: 'GET'
        },
        columns: [
            { data: 'id' },
            { data: 'customer' },
            { data: 'total' },
            { data: 'status', orderable: false },
            { data: 'date' },
            { data: 'actions', orderable: false, searchable: false }
        ],
        order: [[4, 'desc']],
        pageLength: 25,
        responsive: true,
        language: {
            processing: '<div class="flex items-center justify-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><span class="ml-2">Loading...</span></div>'
        }
    });

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
                $('#orders-table').DataTable().ajax.reload();
                closeStatusModal();
            },
            error: function(xhr) {
                showMessage('Failed to update order status', 'error');
            }
        });
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