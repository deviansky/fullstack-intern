<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Memberikan akses penuh kepada admin untuk semua aksi.
     * Ini akan dieksekusi sebelum metode lainnya.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'admin') {
            return true;
        }
        return null; // Lanjutkan ke metode otorisasi lainnya
    }

    /**
     * Menentukan apakah pengguna dapat melihat daftar task.
     * Semua pengguna yang login boleh mencoba melihat, logika filter ada di controller.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Menentukan apakah pengguna dapat melihat detail task tertentu.
     */
    public function view(User $user, Task $task): bool
    {
        // Boleh jika dia yang membuat ATAU dia yang ditugaskan
        return $user->id === $task->created_by || $user->id === $task->assigned_to;
    }

    /**
     * Menentukan apakah pengguna dapat membuat task baru.
     * Semua pengguna yang login boleh mencoba membuat.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Menentukan apakah pengguna dapat mengupdate task.
     */
    public function update(User $user, Task $task): bool
    {
        // Jika yang mau edit adalah Manager
        if ($user->role === 'manager') {
            // Boleh jika task itu untuk dirinya, atau untuk staff mana pun
            return $task->assigned_to === $user->id || $task->assignedTo->role === 'staff';
        }

        // Untuk Staff, hanya boleh jika task itu untuknya atau dibuat olehnya
        return $user->id === $task->created_by || $user->id === $task->assigned_to;
    }

    /**
     * Menentukan apakah pengguna dapat menghapus task.
     * [cite_start]Aturan: Hanya admin atau creator [cite: 59]
     */
    public function delete(User $user, Task $task): bool
    {
        // Jika yang mau hapus adalah Manager
        if ($user->role === 'manager') {
            // Boleh jika dia yang buat, atau task itu untuk staff mana pun
            return $task->created_by === $user->id || $task->assignedTo->role === 'staff';
        }
        // Untuk Staff, hanya boleh jika dia yang buat
        return $user->id === $task->created_by;
    }
}