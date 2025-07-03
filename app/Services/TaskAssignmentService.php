<?php

namespace App\Services;

use App\Models\User;

class TaskAssignmentService
{
    /**
     * Memvalidasi apakah seorang pengguna (assigner) dapat menugaskan
     * task kepada pengguna lain (assignee).
     */
    public function canAssign(User $assigner, User $assignee): bool
    {
        // Admin bisa menugaskan ke siapa saja
        if ($assigner->role === 'admin') {
            return true;
        }

        // Manager hanya bisa menugaskan ke staff
        if ($assigner->role === 'manager' && $assignee->role === 'staff') {
            return true;
        }

        // Selain itu, tidak boleh
        return false;
    }
}