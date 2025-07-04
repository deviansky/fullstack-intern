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
        $userRole = Auth::user()->role;
        if ($userRole !== 'admin' && $userRole !== 'manager') {
            abort(403, 'Akses ditolak.');
        }
        return User::all();
    }

    //Menyimpan pengguna baru.
    public function store(Request $request)
    {
        $creator = Auth::user();
        if (!in_array($creator->role, ['admin', 'manager'])) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['admin', 'manager', 'staff'])],
        ]);

        if ($creator->role === 'manager' && $validatedData['role'] !== 'staff') {
            return response()->json(['message' => 'Manager hanya dapat membuat pengguna dengan role staff.'], 422);
        }

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
            'status' => true,
        ]);

        return response()->json($user, 201);
    }
}