<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'username' => 'required|string|unique:tb_users,username|max:50',
                'email' => 'required|email|unique:tb_users,email',
                'password' => 'required|min:8',
                'no_hp' => 'required|string|min:8'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $e->errors() // ← Android bisa parsing ini
            ], 422); // 422 = Unprocessable Entity
        }

        try {
            // Panggil stored procedure
            DB::statement('CALL RegisterUser(?, ?, ?, ?, ?)', [
                $request->username,
                $request->nama,
                $request->no_hp,
                $request->email,
                bcrypt($request->password) // Hash password dulu
            ]);
    
            // Ambil data user yang baru di-insert (optional)
            $user = DB::table('tb_users')->where('email', $request->email)->first();
    
            // Buat token Laravel Sanctum (jika pakai Sanctum)
            $token = $user ? \App\Models\User::find($user->id_user)->createToken('auth_token')->plainTextToken : null;
    
            return response()->json([
                'message' => 'Registrasi Berhasil',
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(), // atau ambil dari SQLSTATE
            ], 400);
        }
    }


    public function login(Request $request)
    {
        $credentials = $request->only('login', 'password');

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Cari user berdasarkan email atau username
        $user = User::where($field, $credentials['login'])->first();

        //Jika user tidak ditemukan
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun tidak ditemukan.'
            ], 404);
        }

        //Jika password salah
        if (!Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password salah.'
            ], 401);
        }

        //Login sukses
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'user' => $user,
                'token' => $token,
            ]
        ]);
    }


}
