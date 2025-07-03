<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Task;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckOverdueTasks extends Command
{
    protected $signature = 'tasks:check-overdue';
    protected $description = 'Check for overdue tasks and log them';

    public function handle()
    {
        $this->info('Checking for overdue tasks...');

        // Cari task yang due_date-nya sudah lewat dan statusnya bukan 'done'
        $overdueTasks = Task::where('due_date', '<', Carbon::now())
                            ->where('status', '!=', 'done')
                            ->get();

        foreach ($overdueTasks as $task) {
            // Buat log untuk setiap task yang overdue
            ActivityLog::create([
                'user_id' => $task->created_by, // Log diatribusikan ke pembuat task
                'action' => 'task_overdue',
                'description' => 'Task overdue: (' . $task->id . ') - ' . $task->title,
            ]);

            $this->warn('Task ID ' . $task->id . ' is overdue. Logged.');
        }

        $this->info('Overdue task check complete.');
        return 0;
    }
}