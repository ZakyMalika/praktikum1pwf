<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\KategoriController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/about', [AboutController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('about');
});

Route::middleware('auth')->group(function () {
    // Rute statis (tanpa parameter {}) taruh di atas
    Route::get('/product', [ProductController::class, 'index'])->name('product.index');
    Route::post('/product', [ProductController::class, 'store'])->name('product.store');
    Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
    Route::get('/product/export', [ProductController::class, 'export'])->name('product.export')->middleware('can:export-product');

    // Rute dengan parameter tambahan spesifik taruh di tengah
    Route::get('/product/edit/{product}', [ProductController::class, 'edit'])->name('product.edit');
    Route::put('/product/update/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::delete('/product/delete/{id}', [ProductController::class, 'delete'])->name('product.delete');

    // Rute dinamis yang menangkap segalanya (/product/{apapun}) WAJIB ditaruh paling bawah
    Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');


// kategori Rute statis (tanpa parameter {}) taruh di atas
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::get('/kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
    Route::get('/kategori/export', [KategoriController::class, 'export'])->name('kategori.export')->middleware('can:export-kategori');

    // Rute dengan parameter tambahan spesifik taruh di tengah
    Route::get('/kategori/edit/{kategori}', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategori/update/{id}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/delete/{id}', [KategoriController::class, 'delete'])->name('kategori.delete');

    // Rute dinamis yang menangkap segalanya (/kategori/{apapun}) WAJIB ditaruh paling bawah
    Route::get('/kategori/{id}', [KategoriController::class, 'show'])->name('kategori.show');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/about', function () {
//     return view('about');
// })->middleware(['auth', 'verified'])->name('about');





require __DIR__.'/auth.php';
