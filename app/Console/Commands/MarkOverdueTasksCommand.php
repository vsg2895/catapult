<?php

namespace App\Console\Commands;

use App\Models\UserTask;
use App\Notifications\TaskExpirationNotification;

use Illuminate\Console\Command;

class MarkOverdueTasksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:mark-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark overdue tasks';

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
            ->withCount(['referrals'])
            ->whereHas('task', function ($query) {
                return $query->where(function ($q) {
                    $q->where('number_of_winners', 0);
                })
                    ->where('ended_at', '<=', now()->subDays(1))
                    ->withoutGlobalScopes();
            })
            ->whereNotIn('status', [UserTask::STATUS_DONE, UserTask::STATUS_OVERDUE])
            ->get();

        $filteredUserTasks = $userTasks->filter(function ($userTask) {
            $numberOfInvites = $userTask->task->number_of_invites;
            if ($numberOfInvites > 0) {
                return $userTask->referrals_count !== $numberOfInvites;
            }

            return true;
        });

        UserTask::whereIn('id', $filteredUserTasks->pluck('id')->toArray())
            ->update(['status' => UserTask::STATUS_OVERDUE]);

        $filteredUserTasks->each(function (UserTask $userTask) {
            $userTask->user->notify(new TaskExpirationNotification($userTask->task->name));
        });

        return 1;
    }
}
