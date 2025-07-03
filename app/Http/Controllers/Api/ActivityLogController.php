<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Menampilkan daftar activity log.
     */
    public function index()
    {
        // Otorisasi: Hanya admin yang boleh melihat log
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }
        
        return ActivityLog::with('user')->latest('logged_at')->get();
    }
}