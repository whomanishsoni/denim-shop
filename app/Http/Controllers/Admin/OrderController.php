<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return view('admin.orders.index');
    }

    public function data(Request $request)
    {
        $query = Order::with(['user', 'orderItems']);

        // Search
        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('id', 'like', "%{$searchValue}%")
                  ->orWhere('status', 'like', "%{$searchValue}%")
                  ->orWhereHas('user', function($userQuery) use ($searchValue) {
                      $userQuery->where('name', 'like', "%{$searchValue}%")
                               ->orWhere('email', 'like', "%{$searchValue}%");
                  });
            });
        }

        $totalRecords = Order::count();
        $filteredRecords = $query->count();

        // Ordering
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDir = $request->order[0]['dir'];
            $columns = ['id', 'user_id', 'total', 'status', 'created_at'];
            if (isset($columns[$orderColumn])) {
                $query->orderBy($columns[$orderColumn], $orderDir);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        $orders = $query->skip($start)->take($length)->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $orders->map(function($order) {
                return [
                    'id' => '#' . $order->id,
                    'customer' => $order->user->name,
                    'total' => '$' . number_format($order->total, 2),
                    'status' => '<span class="px-2 py-1 text-xs rounded-full ' . $order->status_badge . '">' . ucfirst($order->status) . '</span>',
                    'date' => $order->created_at->format('M d, Y'),
                    'actions' => view('admin.orders.actions', compact('order'))->render()
                ];
            })
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.product']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,shipped,delivered'
        ]);

        $order->update(['status' => $request->status]);

        return response()->json(['success' => 'Order status updated successfully!']);
    }
}