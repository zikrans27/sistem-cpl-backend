<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\MahasiswaProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'nim' => 'required|string|max:50|unique:mahasiswa_profiles,nim',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|min:6|confirmed',
    ]);

    // Cari role mahasiswa
    $role = Role::where('name', 'mahasiswa')->first();

    if (!$role) {
        throw ValidationException::withMessages([
            'role' => ['Role mahasiswa tidak ditemukan.']
        ]);
    }

    // Buat user
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role_id' => $role->id,
    ]);

    // Buat profil mahasiswa
    MahasiswaProfile::create([
        'user_id' => $user->id,
        'nim' => $request->nim,
        'program_studi' => $request->program_studi ?? null,
        'angkatan' => $request->angkatan ?? null,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Registrasi berhasil',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]
    ], 201);
}
    
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cari user by email atau NIM
        $user = User::with('role', 'mahasiswaProfile')
            ->where('email', $request->username)
            ->orWhereHas('mahasiswaProfile', function($q) use ($request) {
                $q->where('nim', $request->username);
            })
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
    return response()->json([
        'message' => 'Username atau password salah.'
    ], 401);
}

        // Cek role sesuai yang dipilih di frontend
        if ($user->role->name !== $request->role) {
            return response()->json([
                'message' => 'Role tidak sesuai.'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'role'  => $user->role->name,
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'nim'   => $user->mahasiswaProfile?->nim,
                'mahasiswa_id' => $user->mahasiswaProfile?->id,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil.']);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('role', 'mahasiswaProfile');
        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role->name,
            'nim'   => $user->mahasiswaProfile?->nim,
        ]);
    }
}