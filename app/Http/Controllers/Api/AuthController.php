<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:50',
            'email'     => 'required|email|unique:tb_users,email',
            'password'  => 'required|min:8'
        ]);

        $user = User::create([
            'id_user'   => 'U'.strtoupper(Str::random(6)),
            'nama'      => $request->nama,
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

    if (!Auth::attempt([$field => $credentials['login'], 'password' => $credentials['password']])) {
        return response()->json(['message' => 'Login gagal'], 401);
    }

    $user = Auth::user();
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
