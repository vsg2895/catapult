<?php

namespace App\Console\Commands;

use App\Models\UserTask;
use Illuminate\Console\Command;

class RemoveManagersFromTasksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:remove-managers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove managers from tasks after few days';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $userTasks = UserTask::where('status', UserTask::STATUS_ON_REVISION)
            ->where('revised_at', '<=', now()->subDays(3))
            ->get();

        UserTask::whereIn('id', $userTasks->pluck('id')->toArray())->update(['status' => UserTask::STATUS_WAITING_FOR_REVIEW, 'manager_id' => null]);
        return 1;
    }
}
