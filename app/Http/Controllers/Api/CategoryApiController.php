<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Kategori;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CategoryApiController extends Controller
{
    /**
     * Display a listing of categories.
     * GET /api/category
     */
    public function index()
    {
        try {
            $categories = Kategori::with('product')->get();

            return response()->json([
                'message' => 'Categories retrieved successfully',
                'data' => $categories
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error mengambil daftar kategori', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error retrieving categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created category in storage.
     * POST /api/category
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $validated = $request->validated();

            $category = Kategori::create($validated);

            Log::info('Menambah data kategori via API', [
                'category' => $category
            ]);

            return response()->json([
                'message' => 'Kategori berhasil ditambahkan!!',
                'data' => $category,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error saat menambah kategori via API', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error creating category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified category.
     * GET /api/category/{id}
     */
    public function show(int $id)
    {
        try {
            $category = Kategori::with('product')->find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Kategori tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'message' => 'Category retrieved successfully',
                'data' => $category
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data kategori', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error retrieving category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified category in storage.
     * PUT /api/category/{id}
     */
    public function update(UpdateCategoryRequest $request, int $id)
    {
        try {
            $category = Kategori::find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Kategori tidak ditemukan',
                ], 404);
            }

            // Authorization check - hanya admin yang bisa update kategori
            if (Auth::user()->role !== 'admin') {
                return response()->json([
                    'message' => 'Anda tidak memiliki hak akses untuk mengupdate kategori',
                ], 403);
            }

            $validated = $request->validated();
            $category->update($validated);

            Log::info('Update data kategori via API', [
                'category' => $category
            ]);

            return response()->json([
                'message' => 'Kategori berhasil diperbarui',
                'data' => $category,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error saat update kategori via API', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error updating category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified category from storage.
     * DELETE /api/category/{id}
     */
    public function destroy(int $id)
    {
        try {
            $category = Kategori::find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Kategori tidak ditemukan',
                ], 404);
            }

            // Authorization check - hanya admin yang bisa delete kategori
            if (Auth::user()->role !== 'admin') {
                return response()->json([
                    'message' => 'Anda tidak memiliki hak akses untuk menghapus kategori',
                ], 403);
            }

            $category->delete();

            Log::info('Hapus data kategori via API', [
                'category_id' => $id
            ]);

            return response()->json([
                'message' => 'Kategori berhasil dihapus',
            ], 204);
        } catch (\Throwable $e) {
            Log::error('Error saat hapus kategori via API', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error deleting category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
