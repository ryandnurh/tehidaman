<?php

namespace App\Events;

use App\Models\Transaksi; // <-- Pastikan namespace Transaksi benar
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPaymentFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Objek transaksi yang gagal atau dibatalkan.
     *
     * @var \App\Models\Transaksi
     */
    public $transaksi;

    /**
     * Buat instance event baru.
     *
     * @param  \App\Models\Transaksi  $transaksi
     * @return void
     */
    public function __construct(Transaksi $transaksi)
    {
        $this->transaksi = $transaksi;
    }

    /**
     * Get the channels the event should broadcast on.
     * (Biarkan kosong jika Anda tidak menggunakan broadcasting real-time saat ini)
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}