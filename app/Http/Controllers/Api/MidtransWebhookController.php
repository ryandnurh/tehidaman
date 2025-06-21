<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Midtrans;
use App\Services\MidtransNotificationService; // Service baru untuk memproses notifikasi

class MidtransWebhookController extends Controller
{
    public function handle(Request $request, MidtransNotificationService $notificationService)
    {
        // Proses notifikasi menggunakan service
        $notificationService->process($request->all());

        // Selalu kembalikan respons 200 OK ke Midtrans
        return response()->json(['status' => 'ok']);
    }
}