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
        return null;
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
        if ($user->role === 'manager') {
            return $task->assigned_to === $user->id || $task->assignedTo->role === 'staff';
        }

        return $user->id === $task->created_by || $user->id === $task->assigned_to;
    }

    /**
     * Menentukan apakah pengguna dapat menghapus task.
     */
    public function delete(User $user, Task $task): bool
    {
        if ($user->role === 'manager') {
            return $task->created_by === $user->id || $task->assignedTo->role === 'staff';
        }
        return $user->id === $task->created_by;
    }
}