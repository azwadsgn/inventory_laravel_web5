<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\LaporanController;

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// ============================
// Autentikasi (Guest only)
// ============================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============================
// Routes yang butuh login
// ============================
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Produk — semua user bisa lihat
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // Produk — hanya admin bisa tambah/edit/hapus
    Route::middleware('admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // Histori Stok — semua user bisa lihat & input
    Route::get('/stock', [StockTransactionController::class, 'index'])->name('stock.index');
    Route::post('/stock/in', [StockTransactionController::class, 'storeIn'])->name('stock.in');
    Route::post('/stock/out', [StockTransactionController::class, 'storeOut'])->name('stock.out');

    // Cetak histori — hanya admin
    Route::middleware('admin')->group(function () {
        Route::get('/stock/print', [StockTransactionController::class, 'print'])->name('stock.print');

        // Laporan Keuangan — hanya admin
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/print', [LaporanController::class, 'print'])->name('laporan.print');
    });
});