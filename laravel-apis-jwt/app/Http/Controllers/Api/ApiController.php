<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    // Register API - Input (name, email, password, password confirmation, image)
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'profile_image' => 'nullable|image'
        ]);

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('users', 'public');
        }

        User::create($data);

        return response()->json([
            'status' => 200,
            'message' => 'Akun berhasil dibuat',
        ]);
    }

    // Login API - Input (email, password)
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Buat token dengan cek apakah email dan password benar
        $token = auth()->attempt($data);

        if ($token) {
            return response()->json([
                'status' => true,
                'message' => 'Berhasil login!',
                'token' => $token
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Gagal login! Email atau Password salah',
            ], 404);
        }
    }

    // Profile API
    public function profile()
    {
        $dataUser = auth()->user();

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil ditemukan!',
            'data' => $dataUser,
            'profile_image' => asset('storage/' . $dataUser->profile_image),
        ]);
    }

    // Refresh API
    public function refreshToken()
    {
        $refreshToken = auth()->refresh();

        return response()->json([
            'status' => true,
            'message' => 'Token berhasil direfresh!',
            'token_baru' => $refreshToken,
        ], 201);
    }

    // Logout API
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'status' => true,
            'message' => 'Berhasil logout! token berhasil direset!'
        ], 200);
    }
}
