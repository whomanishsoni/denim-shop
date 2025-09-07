<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Merge session cart with database cart
            $this->mergeSessionCartToDatabase();

            if (Auth::user()->isAdmin()) {
                return redirect()->intended('/admin');
            }

            return redirect()->intended('/')->with('success', 'Login successful!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
        ]);

        Auth::login($user);

        // Merge session cart with database cart after registration
        $this->mergeSessionCartToDatabase();

        return redirect('/')->with('success', 'Registration successful! Welcome to Denim Store.');
    }

    public function logout(Request $request)
    {
        // Preserve cart session if needed (optional, depending on your preference)
        $cart = $request->session()->get('cart', []);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // Restore cart session (optional, for guest users post-logout)
        $request->session()->put('cart', $cart);

        return redirect('/');
    }

    protected function mergeSessionCartToDatabase()
    {
        $sessionCart = session()->get('cart', []);
        foreach ($sessionCart as $productId => $item) {
            Cart::updateOrCreate(
                ['user_id' => Auth::id(), 'product_id' => $productId],
                ['quantity' => \DB::raw("quantity + {$item['quantity']}")]
            );
        }
        // Clear session cart after merging
        session()->forget('cart');
    }
}