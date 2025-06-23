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
        $request->validate([
            'username'      => 'required|string|unique:tb_users,username|max:50',
            'email'     => 'required|email|unique:tb_users,email',
            'password'  => 'required|min:8'
        ]);

        $user = User::create([
            'id_user'   => 'U'.strtoupper(Str::random(6)),
            'username'      => $request->username,
'no_hp' => $request->no_hp,
            'email'     => $request->email,
            'password'  => bcrypt($request->password)
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'   => 'Registrasi Berhasil',
            'token'     => $token,
            'user'      => $user
        ]);
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
