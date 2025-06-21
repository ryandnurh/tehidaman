<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Midtrans; // Import Midtrans

class MidtransServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Set konfigurasi Midtrans secara global dari file .env
        Midtrans\Config::$serverKey = config('midtrans.server_key');
        Midtrans\Config::$isProduction = config('midtrans.is_production');
        Midtrans\Config::$isSanitized = true;
        Midtrans\Config::$is3ds = config('midtrans.is_3ds');
    }
}