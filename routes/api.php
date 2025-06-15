<?php

use App\Http\Controllers\Api\UserController;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\TokoController;
use App\Http\Controllers\Api\PromoController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentStatusController;

Route::prefix('auth')->group(function(){
    Route::post('register',[AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('user', [UserController::class, 'getUser']);
    Route::post('update-user', [UserController::class, 'updateUser']);
    Route::post('update-password', [UserController::class, 'updatePassword']);
    
    Route::post('tambah-alamat', [UserController::class, 'tambahAlamat']);
    Route::get('get-alamat', [UserController::class, 'getAlamat']);
    Route::post('edit-alamat', [UserController::class, 'editAlamat']);
    Route::post('delete-alamat', [UserController::class, 'deleteAlamat']);

    Route::post('tambah-favorit', [UserController::class, 'tambahFavorit']);
    Route::get('get-favorit', [UserController::class, 'getFavorit']);
    Route::post('delete-favorit', [UserController::class, 'deleteFavorit']);
    
    Route::post('tambah-keranjang', [UserController::class, 'tambahKeranjang']);
    Route::get('get-keranjang', [UserController::class, 'getKeranjang']);
    Route::post('edit-keranjang', [UserController::class, 'editKeranjang']);
    Route::post('delete-keranjang', [UserController::class, 'deleteKeranjang']);

    Route::post('/promo/rekomendasi', [PromoController::class, 'getRecommendations'])->name('promo.recommendations');
    Route::post('/cart/apply-promo', [PromoController::class, 'applyPromoToCart'])->name('cart.applyPromo');

    Route::post('/orders', [OrderController::class, 'placeOrder'])->name('orders.place');
  
    Route::post('/payment/update-status/{id_transaksi}', [PaymentStatusController::class, 'updatePaymentStatus'])->name('payment.updateStatus');

    Route::post('upload-foto', [UserController::class, 'uploadFoto']);
    
    Route::post('logout',[AuthController::class, 'logout']);
});

//produk
Route::get('kategori', [ProdukController::class, 'getKategori']);
Route::get('produk', [ProdukController::class, 'getProduk']);
// Route::get('produk/{id}', [ProdukController::class, 'getProdukById']);
// Route::get('produk/kategori/{id}', [ProdukController::class, 'getProdukByKategori']);

//toko
Route::get('toko', [TokoController::class, 'getToko']);
Route::get('toko/nearest', [TokoController::class, 'findNearestToko']);

// promo
Route::get('promo', [PromoController::class, 'getPromo']);

