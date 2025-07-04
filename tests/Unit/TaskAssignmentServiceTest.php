<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\TaskAssignmentService;
use PHPUnit\Framework\TestCase;

class TaskAssignmentServiceTest extends TestCase
{
    /** @test */
    public function manager_can_assign_a_task_to_a_staff_member(): void
    {
        $service = new TaskAssignmentService();
        $manager = new User(['role' => 'manager']);
        $staff = new User(['role' => 'staff']);

        $this->assertTrue($service->canAssign($manager, $staff));
    }

    /** @test */
    public function manager_cannot_assign_a_task_to_an_admin(): void
    {
        $service = new TaskAssignmentService();

        $manager = new User(['role' => 'manager']);
        $admin = new User(['role' => 'admin']);

        $this->assertFalse($service->canAssign($manager, $admin));
    }

    /** @test */
    public function admin_can_assign_a_task_to_anyone(): void
    {
        $service = new TaskAssignmentService();

        $admin = new User(['role' => 'admin']);
        $staff = new User(['role' => 'staff']);
        $manager = new User(['role' => 'manager']);

        $this->assertTrue($service->canAssign($admin, $staff));
        $this->assertTrue($service->canAssign($admin, $manager));
    }
}