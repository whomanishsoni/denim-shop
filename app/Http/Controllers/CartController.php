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
            'size' => 'required|in:S,M,L,XL',
            'color' => 'required|in:Blue,Black,White',
        ]);

        \Log::info('Add to cart request', ['product_id' => $request->product_id, 'quantity' => $request->quantity, 'size' => $request->size, 'color' => $request->color]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available'
            ], 400);
        }

        if (!in_array($request->size, $product->sizes ?? []) || !in_array($request->color, $product->colors ?? [])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid size or color for this product'
            ], 400);
        }

        if (Auth::check()) {
            // Check if the cart item exists with the same size and color
            $cart = Cart::where('user_id', Auth::id())
                        ->where('product_id', $request->product_id)
                        ->where('size', $request->size)
                        ->where('color', $request->color)
                        ->first();
            if ($cart) {
                // Increment existing quantity
                $cart->quantity += $request->quantity;
                $cart->save();
            } else {
                // Create new cart item with exact quantity, size, and color
                $cart = Cart::create([
                    'user_id' => Auth::id(),
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                    'size' => $request->size,
                    'color' => $request->color,
                ]);
            }
            \Log::info('Database cart updated', ['cart' => $cart->toArray(), 'new_quantity' => $cart->quantity]);
        } else {
            $cart = session()->get('cart', []);
            $cartKey = $request->product_id . '-' . $request->size . '-' . $request->color;

            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] += $request->quantity;
            } else {
                $cart[$cartKey] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $request->quantity,
                    'image' => $product->main_image,
                    'size' => $request->size,
                    'color' => $request->color,
                ];
            }
            session()->put('cart', $cart);
            \Log::info('Session cart updated', ['cart' => $cart, 'new_quantity' => $cart[$cartKey]['quantity']]);
        }

        return $this->cartResponse();
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
            'size' => 'required|in:S,M,L,XL',
            'color' => 'required|in:Blue,Black,White',
        ]);

        $cartKey = $request->product_id . '-' . $request->size . '-' . $request->color;

        if (Auth::check()) {
            if ($request->quantity == 0) {
                Cart::where('user_id', Auth::id())
                    ->where('product_id', $request->product_id)
                    ->where('size', $request->size)
                    ->where('color', $request->color)
                    ->delete();
            } else {
                $product = Product::findOrFail($request->product_id);
                if ($product->stock < $request->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough stock available'
                    ], 400);
                }
                if (!in_array($request->size, $product->sizes ?? []) || !in_array($request->color, $product->colors ?? [])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid size or color for this product'
                    ], 400);
                }
                $cart = Cart::updateOrCreate(
                    [
                        'user_id' => Auth::id(),
                        'product_id' => $request->product_id,
                        'size' => $request->size,
                        'color' => $request->color,
                    ],
                    ['quantity' => $request->quantity]
                );
                \Log::info('Database cart updated (update)', ['cart' => $cart->toArray(), 'new_quantity' => $cart->quantity]);
            }
        } else {
            $cart = session()->get('cart', []);
            if ($request->quantity == 0) {
                unset($cart[$cartKey]);
            } else {
                $product = Product::findOrFail($request->product_id);
                if ($product->stock < $request->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough stock available'
                    ], 400);
                }
                if (!in_array($request->size, $product->sizes ?? []) || !in_array($request->color, $product->colors ?? [])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid size or color for this product'
                    ], 400);
                }
                $cart[$cartKey]['quantity'] = $request->quantity;
            }
            session()->put('cart', $cart);
            \Log::info('Session cart updated (update)', ['cart' => $cart]);
        }

        return $this->cartResponse();
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'size' => 'required|in:S,M,L,XL',
            'color' => 'required|in:Blue,Black,White',
        ]);

        $cartKey = $request->product_id . '-' . $request->size . '-' . $request->color;

        if (Auth::check()) {
            Cart::where('user_id', Auth::id())
                ->where('product_id', $request->product_id)
                ->where('size', $request->size)
                ->where('color', $request->color)
                ->delete();
        } else {
            $cart = session()->get('cart', []);
            unset($cart[$cartKey]);
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
                        'size' => $item->size,
                        'color' => $item->color,
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