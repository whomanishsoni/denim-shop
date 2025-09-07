<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        \Log::info('Authenticated user ID: ' . Auth::id());
        return view('orders.index');
    }

    public function data(Request $request)
    {
        $query = Auth::user()->orders()->with('orderItems.product');

        $totalRecords = Auth::user()->orders()->count();
        $filteredRecords = $query->count();

        // Ordering
        $query->orderBy('created_at', 'desc');

        // Pagination
        $page = $request->page ?? 1;
        $perPage = $request->per_page ?? 10;
        $orders = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'orders' => collect($orders->items())->map(function($order) {
                return [
                    'id' => $order->id,
                    'total' => $order->total,
                    'status' => $order->status,
                    'status_badge' => $order->status_badge,
                    'created_at' => $order->created_at->format('M d, Y'),
                    'items_count' => $order->orderItems->count(),
                ];
            }),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem(),
            ]
        ]);
    }

    public function checkout()
    {
        $cart = $this->getCartItems();

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        return view('orders.checkout', compact('cart'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'phone' => 'required|string',
        ]);

        $cart = $this->getCartItems();

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Validate stock
            foreach ($cart as $item) {
                $product = Product::findOrFail($item['id']);
                if ($product->stock < $item['quantity']) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => "Not enough stock for {$item['name']}"
                    ], 400);
                }
            }

            $total = array_sum(array_map(function($item) {
                return $item['price'] * $item['quantity'];
            }, $cart));

            $order = Order::create([
                'user_id' => Auth::id(),
                'total' => $total,
                'status' => 'pending',
                'shipping_address' => [
                    'address' => $request->address,
                    'city' => $request->city,
                    'postal_code' => $request->postal_code,
                    'phone' => $request->phone,
                ],
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // Update product stock
                Product::where('id', $item['id'])->decrement('stock', $item['quantity']);
            }

            // Clear cart
            if (Auth::check()) {
                Cart::where('user_id', Auth::id())->delete();
            } else {
                session()->forget('cart');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $order->id,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Order creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order'
            ], 500);
        }
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('orderItems.product');
        return view('orders.show', compact('order'));
    }

    private function getCartItems()
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())
                ->with('product')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->product_id,
                        'name' => $item->product->name,
                        'price' => $item->product->price,
                        'quantity' => $item->quantity,
                        'image' => $item->product->main_image,
                    ];
                })->toArray();
        }

        return session()->get('cart', []);
    }
}