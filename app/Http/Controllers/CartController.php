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
        try {
            // Ambil semua item keranjang untuk pengguna yang sedang login
            $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
            $total = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->harga;
            });

            return view('user.cart', compact('cartItems', 'total'));
        } catch (\Exception $e) {
            \Log::error('Error loading cart: ' . $e->getMessage());
            return view('user.cart', ['cartItems' => collect(), 'total' => 0]);
        }
    }

    public function store(Product $product, Request $request)
    {
        try {
            $request->validate([
                'quantity' => 'required|numeric|min:1|max:' . $product->jumlah
            ]);

            // Cek apakah produk sudah ada di keranjang
            $existingCart = Cart::where('user_id', Auth::id())
                                ->where('product_id', $product->id)
                                ->first();

            if ($existingCart) {
                $existingCart->update([
                    'quantity' => $existingCart->quantity + $request->quantity
                ]);
            } else {
                Cart::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'quantity' => $request->quantity
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'Produk ditambahkan ke keranjang');
        } catch (\Exception $e) {
            \Log::error('Error adding to cart: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan produk ke keranjang.');
        }
    }

    public function update(Request $request, Cart $cartItem)
    {
        try {
            $request->validate([
                'quantity' => 'required|numeric|min:1|max:' . $cartItem->product->jumlah
            ]);

            $cartItem->update([
                'quantity' => $request->quantity
            ]);

            return redirect()->route('cart.index')->with('success', 'Keranjang diperbarui');
        } catch (\Exception $e) {
            \Log::error('Error updating cart: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui keranjang.');
        }
    }

    public function destroy(Cart $cartItem)
    {
        try {
            $cartItem->delete();
            return redirect()->route('cart.index')->with('success', 'Produk dihapus dari keranjang');
        } catch (\Exception $e) {
            \Log::error('Error removing from cart: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus produk dari keranjang.');
        }
    }
}
