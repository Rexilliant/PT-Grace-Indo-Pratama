<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');

        $variants = \App\Models\ProductVariant::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('sku', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%")
                        ->orWhere('unit', 'like', "%{$q}%")
                        ->orWhere('status', 'like', "%{$q}%")
                        ->orWhere('pack_size', 'like', "%{$q}%")
                        ->orWhere('price', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.executive-produk-variant', compact('variants', 'q'));
    }

    // FORM ADD VARIANT
    public function create()
    {
        // ambil list produk untuk dropdown product_id
        $products = Product::orderBy('name')->get(['id', 'name', 'code']);

        return view('admin.add-executive-produk-variant', compact('products'));
    }

    // STORE VARIANT KE DB
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'sku' => ['required', 'string', 'max:255', 'unique:product_variants,sku'],
            'name' => ['required', 'string', 'max:255'],
            'pack_size' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        ProductVariant::create($validated);

        return redirect()
            ->route('admin.executive-produk-variant')
            ->with('success', 'Produk varian berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $variant = ProductVariant::findOrFail($id);

        // buat dropdown relasi produk
        $products = Product::orderBy('name')->get(['id', 'code', 'name']);

        return view('admin.edit-executive-produk-variant', compact('variant', 'products'));
    }

    public function update(Request $request, $id)
    {
        $variant = ProductVariant::findOrFail($id);

        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'sku' => ['required', 'string', 'max:255', 'unique:product_variants,sku,' . $variant->id],
            'name' => ['required', 'string', 'max:255'],
            'pack_size' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        $variant->update($validated);

        return redirect()
            ->route('admin.executive-produk-variant')
            ->with('success', 'Produk varian berhasil diperbarui.');
    }
}
