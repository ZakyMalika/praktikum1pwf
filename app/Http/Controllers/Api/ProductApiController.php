<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductApiController extends Controller
{
    /**
     * Display a listing of products.
     * GET /api/product
     */
    public function index()
    {
        try {
            $products = Product::with('user')->get();

            return response()->json([
                'message' => 'Products retrieved successfully',
                'data' => $products
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error mengambil daftar produk', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error retrieving products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created product in storage.
     * POST /api/product
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $validated = $request->validated();

            $validated['user_id'] = Auth::id();

            $product = Product::create($validated);

            Log::info('Menambah data produk via API', [
                'product' => $product
            ]);

            return response()->json([
                'message' => 'Produk berhasil ditambahkan!!',
                'data' => $product,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error saat menambah product via API', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error creating product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product.
     * GET /api/product/{id}
     */
    public function show(int $id)
    {
        try {
            $product = Product::with('user')->find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'message' => 'Product retrieved successfully',
                'data' => $product
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data produk', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error retrieving product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified product in storage.
     * PUT /api/product/{id}
     */
    public function update(UpdateProductRequest $request, int $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product tidak ditemukan',
                ], 404);
            }

            // Authorization check - hanya owner atau admin yang bisa update
            if (Auth::id() !== $product->user_id && Auth::user()->role !== 'admin') {
                return response()->json([
                    'message' => 'Anda tidak memiliki hak akses untuk mengupdate produk ini',
                ], 403);
            }

            $validated = $request->validated();
            $product->update($validated);

            Log::info('Update data produk via API', [
                'product' => $product
            ]);

            return response()->json([
                'message' => 'Produk berhasil diperbarui',
                'data' => $product,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error saat update product via API', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error updating product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product from storage.
     * DELETE /api/product/{id}
     */
    public function destroy(int $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product tidak ditemukan',
                ], 404);
            }

            // Authorization check - hanya owner atau admin yang bisa delete
            if (Auth::id() !== $product->user_id && Auth::user()->role !== 'admin') {
                return response()->json([
                    'message' => 'Anda tidak memiliki hak akses untuk menghapus produk ini',
                ], 403);
            }

            $product->delete();

            Log::info('Hapus data produk via API', [
                'product_id' => $id
            ]);

            return response()->json([
                'message' => 'Produk berhasil dihapus',
            ], 204);
        } catch (\Throwable $e) {
            Log::error('Error saat hapus product via API', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error deleting product',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
