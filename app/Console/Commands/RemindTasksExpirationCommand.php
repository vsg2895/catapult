<?php

namespace App\Console\Commands;

use App\Models\UserTask;

use App\Notifications\RemindTaskExpirationNotification;
use Illuminate\Console\Command;

class RemindTasksExpirationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:remind-expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remind tasks expiration';

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
        $userTasks = UserTask::with(['user', 'task'])
            ->where('status', '!=', UserTask::STATUS_DONE)
            ->where('notified', false)
            ->whereHas('task', function ($query) {
                $query->whereRaw('SUBDATE(date(`ended_at`), 2) <= CURDATE()');
            })
            ->get();

        UserTask::whereIn('id', $userTasks->pluck('id')->toArray())->update(['notified' => true]);

        $userTasks->each(function (UserTask $userTask) {
            $userTask->user->notify(new RemindTaskExpirationNotification($userTask->task->name));
        });

        return 1;
    }
}
