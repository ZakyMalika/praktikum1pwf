<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate; // <-- TAMBAHKAN IMPORT INI
use App\Http\Requests\SaveProductRequest; // Pastikan ini di-import

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(10);
        // $products = Product::all();

        return view('product.index', compact('products'));
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'quantity' => 'required|integer',
    //         'price' => 'required|numeric',
    //         'user_id' => 'required|exists:users,id',
    //     ]);
    //     // $validatedData = $request->validated();

    //     $product = Product::create($validated);

    //     return redirect()->route('product.index')->with('success', 'Product created successfully.');
    // }

    public function store(SaveProductRequest $request) 
{
    // Jika sampai baris ini, berarti validasi sudah lolos
    // Kamu bisa mengambil data yang sudah divalidasi dengan:
    $validatedData = $request->validated();

    Product::create($validatedData);

    return redirect()->route('product.index')->with('success', 'Produk berhasil ditambahkan.');
}

    public function create()
    {
        $users = User::orderBy('name')->get();

        return view('product.create', compact('users'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        return view('product.view', compact('product'));
    }

    // public function update(Request $request, $id)
    // {
    //     $product = Product::findOrFail($id);

    //     // 🔒 OTORISASI: Cek apakah user berhak melakukan update
    //     Gate::authorize('update', $product);

    //     $validated = $request->validate([
    //         'name' => 'sometimes|string|max:255',
    //         'quantity' => 'sometimes|integer',
    //         'price' => 'sometimes|numeric',
    //         'user_id' => 'sometimes|exists:users,id',
    //     ]);
        

    //     $product->update($validated);

    //     return redirect()->route('product.index')->with('success', 'Product updated successfully.');
    // }

    public function update(SaveProductRequest $request, $id) 
    {
        $product = Product::findOrFail($id);

        Gate::authorize('update', $product);

        // Mengambil data yang sudah divalidasi dengan aman
        $validatedData = $request->validated();

        $product->update($validatedData);

        return redirect()->route('product.index')->with('success', 'Product updated successfully.');
    }

    

    public function edit(Product $product)
    {
        // 🔒 OTORISASI: Cek apakah user berhak melihat halaman edit
        Gate::authorize('update', $product);

        $users = User::orderBy('name')->get();

        return view('product.edit', compact('product', 'users'));
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);

        // 🔒 OTORISASI: Cek apakah user berhak menghapus data
        Gate::authorize('delete', $product);

        $product->delete();

        return redirect()->route('product.index')->with('success', 'Product berhasil dihapus');
    }

}