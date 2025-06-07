<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProdukController;


Route::prefix('auth')->group(function(){
    Route::post('register',[AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    
    //produk
    Route::get('kategori', [ProdukController::class, 'getKategori']);
    Route::get('produk', [ProdukController::class, 'getProduk']);
    Route::get('produk/{id}', [ProdukController::class, 'getProdukById']);
    Route::get('produk/kategori/{id}', [ProdukController::class, 'getProdukByKategori']);
    
    
    Route::post('logout',[AuthController::class, 'logout']);
});



