<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // CREATE (FORM TAMBAH)
    public function createExecutive()
    {
        return view('admin.add-executive-produk-baru');
    }

    // STORE (SIMPAN)
    public function storeExecutive(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:products,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        Product::create($validated);

        return redirect()
            ->route('admin.executive-produk')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    // EDIT (FORM EDIT) - AMBIL DARI DB
    public function editExecutive($id)
    {
        $product = Product::findOrFail($id);

        // pastikan view ini sesuai file kamu:
        // resources/views/admin/edit-executive-produk.blade.php
        return view('admin.edit-executive-produk', compact('product'));
    }

    // UPDATE (SIMPAN PERUBAHAN)
    public function updateExecutive(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:products,code,' . $product->id],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        $product->update($validated);

        return redirect()
            ->route('admin.executive-produk')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function indexExecutive(Request $request)
    {
        $q = $request->get('q');

        $products = Product::query()
            ->when($q, function ($query) use ($q) {
                $query->where('code', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('status', 'like', "%{$q}%");
            })
            ->orderByDesc('created_at')
            ->paginate(5)
            ->withQueryString();

        return view('admin.executive-produk', compact('products', 'q'));
    }

    public function destroyExecutive($id)
    {
        $product = Product::withTrashed()->findOrFail($id); // aman kalau sudah sempat soft-deleted
        $product->forceDelete(); // HAPUS PERMANEN dari database

        return redirect()
            ->route('admin.executive-produk')
            ->with('success', 'Produk berhasil dihapus!!!.');
    }

}
