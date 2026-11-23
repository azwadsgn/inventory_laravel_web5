<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Daftar produk dengan search & filter
    public function index(Request $request)
    {
        $categories = Category::all();
        $query      = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->get();

        return view('products.index', compact('products', 'categories'));
    }

    // Simpan produk baru (admin only)
    public function store(Request $request)
    {
        $request->validate([
            'sku'         => 'required|unique:products,sku|max:50',
            'name'        => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
        ]);

        Product::create($request->only(['sku', 'name', 'category_id', 'price', 'stock']));

        return redirect()->route('products.index')
                         ->with('success', 'Produk berhasil ditambahkan.');
    }

    // Update produk (admin only)
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'sku'         => 'required|unique:products,sku,' . $product->id . '|max:50',
            'name'        => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'price'       => 'required|numeric|min:0',
        ]);

        $product->update($request->only(['sku', 'name', 'category_id', 'price']));

        return redirect()->route('products.index')
                         ->with('success', 'Produk berhasil diperbarui.');
    }

    // Hapus produk (admin only)
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
                         ->with('success', 'Produk berhasil dihapus.');
    }
}