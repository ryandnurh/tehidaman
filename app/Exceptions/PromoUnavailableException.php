<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromoUnavailableException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     *
     * Method render() ini secara otomatis akan dipanggil oleh Laravel
     * setiap kali exception ini dilempar (thrown) dan tidak ditangkap (uncaught)
     * di tempat lain. Ini memberi kita kontrol penuh atas respons JSON yang dikembalikan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(Request $request): JsonResponse
    {
        // Kita ingin agar saat error ini terjadi, Laravel selalu mengembalikan
        // respons JSON dengan pesan error dan kode status HTTP 422.
        return response()->json([
            'message' => $this->getMessage() // Mengambil pesan yang kita berikan saat melempar exception
        ], 422); // 422 Unprocessable Entity - Kode yang tepat untuk error validasi atau bisnis
    }
}