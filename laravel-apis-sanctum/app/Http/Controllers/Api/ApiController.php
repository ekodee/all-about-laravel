<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Mime\Email;

class ApiController extends Controller
{
    // Register
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            // tambahan validasi confirmed untuk password
            // perlu ada tambahan form konfirmasi password di register nanti
            'password' => 'required|confirmed'
        ]);

        User::create($data);

        return response()->json([
            'status' => 200,
            'message' => 'Akun berhasil dibuat',
        ]);
    }

    // Login
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Ambil data akun user yang login
        $user = User::where('email', $request->email)->first();

        // Cek apakah email terdaftar
        if ($user) {
            // Cek apakah password benar
            if (Hash::check($request->password, $user->password)) {
                // Kalo email terdaftar dan password benar
                // Buat token api
                $token = $user->createToken('myToken')->plainTextToken;

                return response()->json([
                    'status' => 200,
                    'message' => 'Login berhasil!',
                    'token' => $token,
                ]);
            } else {
                // Response jika password salah
                return response()->json([
                    'status' => false,
                    'message' => 'Login gagal, password salah',
                ]);
            }
        } else {
            // Response jika email salah/tidak terdaftar
            return response()->json([
                'status' => false,
                'message' => 'Login gagal, email tidak terdaftar',
            ]);
        }
    }

    // Profile
    public function profile()
    {
        // Jika memiliki token maka ambil data user kirim ke user
        $userData = auth()->user();

        return response()->json([
            'status' => 200,
            'message' => 'Data berhasil ditampilkan',
            'data' => $userData,
            'id' => $userData->id,
        ]);
    }

    // Logout
    public function logout()
    {
        // hapus api token yang dimiliki user
        auth()->user()->tokens()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Logout berhasil! token dihapus',
        ]);
    }
}
