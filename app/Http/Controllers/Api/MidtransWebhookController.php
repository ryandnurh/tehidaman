<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Midtrans; // Import Midtrans
use App\Services\MidtransNotificationService; // Import Service Anda

class MidtransWebhookController extends Controller
{
    protected $notificationService;

    public function __construct(MidtransNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Menerima notifikasi dari Midtrans.
     */
    public function handle(Request $request)
    {
        // 1. Buat instance Notification. Konstruktornya akan otomatis membaca raw body.
        // JANGAN berikan argumen apa pun ke dalamnya.
        $notification = new Midtrans\Notification();

        // 2. Proses notifikasi menggunakan service, dengan mengirim objek notification.
        $this->notificationService->process($notification);

        // 3. Selalu kembalikan respons 200 OK ke Midtrans.
        return response()->json(['status' => 'ok']);
    }
}
