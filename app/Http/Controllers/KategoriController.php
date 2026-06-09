<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\User;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate; // <-- TAMBAHKAN IMPORT INI
use App\Http\Requests\SavekategoriRequest; // Pastikan ini di-import

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::paginate(10);
        // $kategoris = Kategori::all();

        return view('kategori.index', compact('kategoris'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:kategoris,name',
            'product_id' => 'required|exists:products,id',
        ]);

        Kategori::create($validated);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function create()
    {
        $products = \App\Models\Product::orderBy('name')->get();

        return view('kategori.create', compact('products'));
    }

    public function show($id)
    {
        $kategori = Kategori::findOrFail($id);

        return view('kategori.view', compact('kategori'));
    }

    // public function update(Request $request, $id)
    // {
    //     $kategori = Kategori::findOrFail($id);

    //     // 🔒 OTORISASI: Cek apakah user berhak melakukan update
    //     Gate::authorize('update', $kategori);

    //     $validated = $request->validate([
    //         'name' => 'sometimes|string|max:255',
    //         'quantity' => 'sometimes|integer',
    //         'price' => 'sometimes|numeric',
    //         'user_id' => 'sometimes|exists:users,id',
    //     ]);
        

    //     $kategori->update($validated);

    //     return redirect()->route('kategori.index')->with('success', 'kategori updated successfully.');
    // }

    // public function update(SavekategoriRequest $request, $id) 
    // {
    //     $kategori = Kategori::findOrFail($id);

    //     Gate::authorize('update', $kategori);

    //     // Mengambil data yang sudah divalidasi dengan aman
    //     $validatedData = $request->validated();

    //     $kategori->update($validatedData);

    //     return redirect()->route('kategori.index')->with('success', 'kategori updated successfully.');
    // }

    

    public function edit(Kategori $kategori)
    {
        // 🔒 OTORISASI: Cek apakah user berhak melihat halaman edit
        Gate::authorize('update', $kategori);

        $products = \App\Models\Product::orderBy('name')->get();

        return view('kategori.edit', compact('kategori', 'products'));
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        Gate::authorize('update', $kategori);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:kategoris,name,' . $id,
            'product_id' => 'required|exists:products,id',
        ]);

        $kategori->update($validated);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui');
    }

    public function delete($id)
    {
        $kategori = Kategori::findOrFail($id);

        // 🔒 OTORISASI: Cek apakah user berhak menghapus data
        Gate::authorize('delete', $kategori);

        $kategori->delete();

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus');
    }

}