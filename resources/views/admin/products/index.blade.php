@extends('layouts.admin')

@section('title', 'Products Management')
@section('header', 'Products')

@section('content')
<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">All Products</h2>
            <a href="{{ route('admin.products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Add Product
            </a>
        </div>
    </div>
    
    <div class="p-6">
        <table id="products-table" class="w-full">
            <thead>
                <tr>
                    <th class="text-left">#</th>
                    <th class="text-left">Image</th>
                    <th class="text-left">Name</th>
                    <th class="text-left">Category</th>
                    <th class="text-left">Sizes</th>
                    <th class="text-left">Colors</th>
                    <th class="text-left">Price</th>
                    <th class="text-left">Stock</th>
                    <th class="text-left">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#products-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.products.data") }}',
            type: 'GET'
        },
        columns: [
            {
                data: 'counter',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'main_image',
                render: function(data, type, row) {
                    return '<img src="' + data + '" alt="Product Image" class="w-12 h-12 object-cover rounded-md">';
                },
                orderable: false,
                searchable: false
            },
            { data: 'name' },
            { data: 'category' },
            { data: 'sizes' },
            { data: 'colors' },
            { data: 'price' },
            { data: 'stock' },
            { data: 'actions', orderable: false, searchable: false }
        ],
        order: [[2, 'desc']],
        pageLength: 10,
        responsive: true,
        language: {
            processing: '<div class="flex items-center justify-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><span class="ml-2">Loading...</span></div>'
        }
    });
});

function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        $.ajax({
            url: '/admin/products/' + id,
            method: 'DELETE',
            success: function(data) {
                showMessage(data.success, 'success');
                $('#products-table').DataTable().ajax.reload();
            },
            error: function(xhr) {
                showMessage('Failed to delete product', 'error');
            }
        });
    }
}
</script>
@endpush