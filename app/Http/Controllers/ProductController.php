<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        try {
            $products = Product::latest()->get();
            return view('dashboard', compact('products'));
        } catch (\Exception $e) {
            \Log::error('Error loading products: ' . $e->getMessage());
            return view('dashboard', ['products' => collect()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required',
                'jumlah' => 'required|integer',
                'harga' => 'required|integer',
                'photo' => 'nullable|image|max:2048',
            ]);

            if ($request->hasFile('photo')) {
                $validated['photo'] = $request->file('photo')->store('products', 'public');
            }

            Product::create($validated);

            return redirect()->back()->with('success', 'Produk berhasil ditambahkan!');
        } catch (\Exception $e) {
            \Log::error('Error creating product: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan produk. Silakan coba lagi.');
        }
    }

    public function edit(Product $product)
    {
        try {
            return response()->json($product);
        } catch (\Exception $e) {
            \Log::error('Error fetching product: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch product'], 500);
        }
    }

    public function update(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required',
                'jumlah' => 'required|integer',
                'harga' => 'required|integer',
                'category' => 'required',
                'photo' => 'nullable|image|max:2048',
            ]);

            if ($request->hasFile('photo')) {
                if ($product->photo) {
                    Storage::disk('public')->delete($product->photo);
                }
                $validated['photo'] = $request->file('photo')->store('products', 'public');
            }

            $product->update($validated);
            return redirect()->back()->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            \Log::error('Error updating product: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui produk. Silakan coba lagi.');
        }
    }

    public function destroy(Product $product)
    {
        try {
            if ($product->photo) {
                Storage::disk('public')->delete($product->photo);
            }

            $product->delete();
            return redirect()->back()->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            \Log::error('Error deleting product: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus produk. Silakan coba lagi.');
        }
    }
}
