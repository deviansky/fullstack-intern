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
        if ($assigner->role === 'admin') {
            return true;
        }

        if ($assigner->role === 'manager' && $assignee->role === 'staff') {
            return true;
        }

        return false;
    }
}