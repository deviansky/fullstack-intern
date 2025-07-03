<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna.
     */
    public function index()
    {
        // Otorisasi: Hanya admin dan manager yang boleh mengakses
        $userRole = Auth::user()->role;
        if ($userRole !== 'admin' && $userRole !== 'manager') {
            abort(403, 'Akses ditolak.');
        }

        return User::all();
    }

    /**
     * Menyimpan pengguna baru.
     */
    public function store(Request $request)
    {
        // Otorisasi: Hanya admin yang boleh membuat pengguna baru
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['admin', 'manager', 'staff'])],
            'status' => 'required|boolean',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
            'status' => $validatedData['status'],
        ]);

        return response()->json($user, 201);
    }
}