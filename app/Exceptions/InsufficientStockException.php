<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InsufficientStockException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     *
     * Method ini akan dipanggil oleh Laravel setiap kali exception ini dilempar.
     * Kita akan menggunakannya untuk mengirim respons JSON yang sesuai.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(Request $request): JsonResponse
    {
        // Kembalikan respons JSON dengan pesan error dari exception
        // dan kode status 422 (Unprocessable Entity).
        return response()->json([
            'message' => $this->getMessage() 
        ], 422); 
    }
}