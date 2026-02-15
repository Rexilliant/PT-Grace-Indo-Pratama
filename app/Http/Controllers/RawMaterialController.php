<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RawMaterial;
use App\Models\RawMaterialStock;

class RawMaterialController extends Controller
{
    public function index()
    {
        $materials = RawMaterial::select('id', 'code', 'name', 'status')
            ->orderByDesc('id')
            ->paginate(5)
            ->withQueryString();

        return view('admin.gudang-bahan-baku', compact('materials'));
    }




    public function stockIndex()
    {
        $stocks = RawMaterial::join(
            'raw_material_stocks',
            'raw_materials.id',
            '=',
            'raw_material_stocks.raw_material_id'
        )
            ->select(
                'raw_materials.id as id',          // ✅ id buat route edit-bahan-baku
                'raw_materials.code',
                'raw_materials.name',
                'raw_materials.status',
                'raw_material_stocks.province',
                'raw_material_stocks.stock'
            )
            ->orderBy('raw_material_stocks.id', 'desc')
            ->paginate(5);

        return view('admin.gudang-stok-bahan-baku', compact('stocks'));
    }


    /**
     * FORM TAMBAH
     */
    public function create()
    {
        return view('admin.add-bahan-baku');
    }

    /**
     * SIMPAN (4 input)
     * + bikin stok default di raw_material_stocks
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|unique:raw_materials,code',
            'bahan_baku' => 'required',
            'unit' => 'required',
            'status' => 'required',
        ]);

        $material = RawMaterial::create([
            'code' => $request->kode_barang,
            'name' => $request->bahan_baku,
            'unit' => $request->unit,
            'status' => $request->status,
        ]);

        // bikin stok default supaya stok page ada row-nya
        // RawMaterialStock::create([
        //     'raw_material_id' => $material->id,
        //     'province' => 'Belum diisi',
        //     'stock' => 0,
        // ]);

        return redirect()
            ->route('admin.gudang-bahan-baku')
            ->with('success', 'Bahan baku berhasil ditambahkan!');
    }

    /**
     * FORM EDIT (master barang aja)
     */
    public function edit($id)
    {
        $material = RawMaterial::with('stock')->findOrFail($id);

        return view('admin.edit-bahan-baku', compact('material'));
    }

    /**
     * UPDATE (master barang aja)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_barang' => 'required|unique:raw_materials,code,' . $id,
            'bahan_baku' => 'required',
            'unit' => 'required',
            'status' => 'required',
        ]);

        $material = RawMaterial::findOrFail($id);

        $material->update([
            'code' => $request->kode_barang,
            'name' => $request->bahan_baku,
            'unit' => $request->unit,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.gudang-bahan-baku')
            ->with('success', 'Bahan baku berhasil diperbarui!');
    }
}
