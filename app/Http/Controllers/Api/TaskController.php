<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /**
     * Menampilkan daftar task sesuai role.
     */
    public function index()
    {
        // Otorisasi: Memanggil viewAny() di TaskPolicy
        $this->authorize('viewAny', Task::class);

        $user = Auth::user();

        if ($user->role === 'admin') {
            $tasks = Task::with('assignedTo', 'createdBy')->latest()->get();
        } else {
            /** @var \Illuminate\Database\Eloquent\Collection<\App\Models\Task> $tasks */
            $tasks = Task::where('created_by', $user->id)
                         ->orWhere('assigned_to', $user->id)
                         ->with('assignedTo', 'createdBy')
                         ->latest()->get();
        }

        return response()->json($tasks);
    }

    /**
     * Menyimpan task baru.
     */
    public function store(Request $request)
    {
        // Otorisasi: Memanggil create() di TaskPolicy
        $this->authorize('create', Task::class);

        $user = Auth::user();

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_to' => 'required|uuid|exists:users,id',
            'due_date' => 'required|date',
        ]);

        if ($user->role === 'manager') {
            $assignedUser = User::find($validatedData['assigned_to']);
            if (!$assignedUser || $assignedUser->role !== 'staff') {
                return response()->json(['message' => 'Manager hanya bisa menugaskan task ke staff.'], 422);
            }
        }

        $task = Task::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'assigned_to' => $validatedData['assigned_to'],
            'due_date' => $validatedData['due_date'],
            'status' => 'pending',
            'created_by' => $user->id,
        ]);

        return response()->json($task, 201);
    }

    /**
     * Mengupdate task yang ada.
     */
    public function update(Request $request, Task $task)
    {
        // Otorisasi: Memanggil update() di TaskPolicy
        $this->authorize('update', $task);

        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'status' => ['sometimes', 'required', Rule::in(['pending', 'in progress', 'done'])],
            'due_date' => 'sometimes|required|date',
        ]);

        $task->update($validatedData);

        return response()->json($task);
    }

    /**
     * Menghapus sebuah task.
     */
    public function destroy(Task $task)
    {
        // Otorisasi: Memanggil delete() di TaskPolicy
        $this->authorize('delete', $task);

        $task->delete();
        
        return response()->json(['message' => 'Task berhasil dihapus.']);
    }
}