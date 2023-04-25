<?php

namespace App\Console\Commands;

use App\Models\{Task, TaskReward};

use Illuminate\Console\Command;

class BackportCoinsTasksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:backport-coins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backport coins tasks';

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
        $tasks = Task::all();
        $rewards = [];

        $tasks->each(function ($task) use (&$rewards) {
            $rewards[] = [
                'type' => 'coins',
                'value' => $task->getRawOriginal('coins'),
                'task_id' => $task->id,
            ];
        });

        TaskReward::insert($rewards);
        return 1;
    }
}
