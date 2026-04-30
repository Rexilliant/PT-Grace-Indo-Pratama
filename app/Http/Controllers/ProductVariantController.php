<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;

class ProductVariantController extends Controller
{
    public function export(Request $request)
    {
        $q = ProductVariant::query()->orderBy('created_at', 'desc');

        if ($request->filled('sku')) {
            $q->where('sku', 'like', '%'.$request->sku.'%');
        }

        if ($request->filled('name')) {
            $q->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        $rows = $q->get()->map(function ($p) {
            return [
                'SKU' => $p->sku,
                'Nama Varian' => $p->name,
                'Produk' => $p->product->name ?? '-',
                'Status' => $p->status,
            ];
        });

        $export = new class($rows) implements FromCollection, WithHeadings
        {
            public function __construct(private $rows) {}

            public function collection()
            {
                return $this->rows;
            }

            public function headings(): array
            {
                return ['SKU', 'Nama Varian', 'Produk', 'Status'];
            }
        };

        return Excel::download($export, 'produk-variant-' . date('Y-m-d') . '.xlsx');
    }
    public function index(Request $request)
    {
        $query = ProductVariant::query()->latest();

        if ($request->filled('sku')) {
            $query->where('sku', 'like', '%'.$request->sku.'%');
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100, 500]) ? $perPage : 10;

        $variants = $query->paginate($perPage)->withQueryString();

        return view('admin.executive-produk-variant', compact('variants'));
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
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $variant = ProductVariant::create([
            'product_id' => $validated['product_id'],
            'sku' => $validated['sku'],
            'name' => $validated['name'],
            'pack_size' => $validated['pack_size'],
            'unit' => $validated['unit'],
            'price' => $validated['price'],
            'status' => $validated['status'],
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $fileName = now()->format('Ymd_His').'_'.rand(100, 999).'.'.$extension;

            $variant
                ->addMedia($file)
                ->usingFileName($fileName)
                ->toMediaCollection('product_variant_image');
        }

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
            'sku' => ['required', 'string', 'max:255', 'unique:product_variants,sku,'.$variant->id],
            'name' => ['required', 'string', 'max:255'],
            'pack_size' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $variant->update([
            'product_id' => $validated['product_id'],
            'sku' => $validated['sku'],
            'name' => $validated['name'],
            'pack_size' => $validated['pack_size'],
            'unit' => $validated['unit'],
            'price' => $validated['price'],
            'status' => $validated['status'],
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $fileName = now()->format('Ymd_His').'_'.rand(100, 999).'.'.$extension;

            $variant
                ->addMedia($file)
                ->usingFileName($fileName)
                ->toMediaCollection('product_variant_image');
        }

        return redirect()
            ->route('admin.executive-produk-variant')
            ->with('success', 'Produk varian berhasil diperbarui.');
    }
}
