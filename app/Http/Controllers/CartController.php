<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        \Log::info('Cart items fetched', ['cartItems' => $cartItems]);
        return view('cart.index', compact('cartItems'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        \Log::info('Add to cart request', ['product_id' => $request->product_id, 'quantity' => $request->quantity]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available'
            ], 400);
        }

        if (Auth::check()) {
            // Check if the cart item exists
            $cart = Cart::where('user_id', Auth::id())->where('product_id', $request->product_id)->first();
            if ($cart) {
                // Increment existing quantity
                $cart->quantity += $request->quantity;
                $cart->save();
            } else {
                // Create new cart item with exact quantity
                $cart = Cart::create([
                    'user_id' => Auth::id(),
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                ]);
            }
            \Log::info('Database cart updated', ['cart' => $cart->toArray(), 'new_quantity' => $cart->quantity]);
        } else {
            $cart = session()->get('cart', []);
            $productId = $request->product_id;

            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] += $request->quantity;
            } else {
                $cart[$productId] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $request->quantity,
                    'image' => $product->main_image,
                ];
            }
            session()->put('cart', $cart);
            \Log::info('Session cart updated', ['cart' => $cart, 'new_quantity' => $cart[$productId]['quantity']]);
        }

        return $this->cartResponse();
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $productId = $request->product_id;

        if (Auth::check()) {
            if ($request->quantity == 0) {
                Cart::where('user_id', Auth::id())->where('product_id', $productId)->delete();
            } else {
                $product = Product::findOrFail($productId);
                if ($product->stock < $request->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough stock available'
                    ], 400);
                }
                $cart = Cart::updateOrCreate(
                    ['user_id' => Auth::id(), 'product_id' => $productId],
                    ['quantity' => $request->quantity]
                );
                \Log::info('Database cart updated (update)', ['cart' => $cart->toArray(), 'new_quantity' => $cart->quantity]);
            }
        } else {
            $cart = session()->get('cart', []);
            if ($request->quantity == 0) {
                unset($cart[$productId]);
            } else {
                $product = Product::findOrFail($productId);
                if ($product->stock < $request->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough stock available'
                    ], 400);
                }
                $cart[$productId]['quantity'] = $request->quantity;
            }
            session()->put('cart', $cart);
            \Log::info('Session cart updated (update)', ['cart' => $cart]);
        }

        return $this->cartResponse();
    }

    public function remove(Request $request)
    {
        $productId = $request->product_id;

        if (Auth::check()) {
            Cart::where('user_id', Auth::id())->where('product_id', $productId)->delete();
        } else {
            $cart = session()->get('cart', []);
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return $this->cartResponse();
    }

    public function count()
    {
        $count = 0;
        if (Auth::check()) {
            $count = Cart::where('user_id', Auth::id())->sum('quantity');
        } else {
            $cart = session()->get('cart', []);
            $count = array_sum(array_column($cart, 'quantity'));
        }

        return response()->json(['count' => $count]);
    }

    private function getCartItems()
    {
        if (Auth::check()) {
            $items = Cart::where('user_id', Auth::id())
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
            \Log::info('Database cart items', ['items' => $items]);
            return $items;
        }

        $cart = session()->get('cart', []);
        \Log::info('Session cart items', ['cart' => $cart]);
        return $cart;
    }

    private function cartResponse()
    {
        $cart = $this->getCartItems();
        $cartCount = array_sum(array_column($cart, 'quantity'));
        $cartTotal = array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal,
        ]);
    }
}