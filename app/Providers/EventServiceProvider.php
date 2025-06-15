<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

// Import Event dan Listener Anda di atas
use App\Events\OrderPaymentFailedOrCancelled;
use App\Events\OrderPaid;
use App\Listeners\RollbackPromoQuotaListener;
use App\Listeners\RollbackStockListener;
// ... (listener lain)

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        OrderPaymentFailed::class => [
            RollbackPromoQuotaListener::class, // Listener untuk mengembalikan kuota promo
            RollbackStockListener::class,      // Listener untuk mengembalikan stok produk
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    // ...
}