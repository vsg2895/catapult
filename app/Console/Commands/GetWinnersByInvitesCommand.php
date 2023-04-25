<?php

namespace App\Console\Commands;

use App\Models\{
    User,
    Task,
    UserTask,
};

use App\Notifications\{
    TaskLoserOfContestNotification,
    TaskWinnerOfContestNotification,
};

use Illuminate\Console\Command;

class GetWinnersByInvitesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:winners-by-invites';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get winners by invites';

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
        $tasks = Task::where('number_of_winners', '>', 0)
            ->where('invite_contest_completed', false)
            ->with([
                'userTasks',
                'userTasks.referrals',
            ])
            ->withoutGlobalScopes()
            ->get();

        $tasks = $tasks->filter(static function (Task $task) {
            return $task->ended_at < now()->subDays(1);
        });

        // TODO: REFACTOR!!!
        $tasks->each(static function (Task $task) {
            $taskWinners = $task->userTasks->filter(static function ($userTask) {
                return $userTask->referrals->count() > 0;
            })
                ->sortByDesc(fn ($userTask) => $userTask->referrals->count())
                ->slice(0, $task->number_of_winners);

            $winnerTaskIds = $taskWinners->pluck('id')->toArray();

            $taskLosers = $task->userTasks->filter(static function ($userTask) use ($winnerTaskIds) {
                return !in_array($userTask->id, $winnerTaskIds, true);
            });

            $loserTaskUsers = User::whereIn('id', $taskLosers->pluck('user_id')->toArray())->get();
            $winnerTaskUsers = User::whereIn('id', $taskWinners->pluck('user_id')->toArray())->get();

            $loserTaskUsers->each(function (User $loserTaskUser) use ($task, $taskLosers) {
                $userTask = $taskLosers->first(fn ($taskLoser) => $taskLoser->user_id === $loserTaskUser->id);
                if ($userTask) {
                    $loserTaskUser->notify(new TaskLoserOfContestNotification($userTask->id, $task->name));
                }
            });

            $winnerTaskUsers->each(function (User $winnerTaskUser) use ($task, $taskWinners) {
                $userTask = $taskWinners->first(fn ($taskWinner) => $taskWinner->user_id === $winnerTaskUser->id);
                if ($userTask) {
                    $winnerTaskUser->notify(new TaskWinnerOfContestNotification($userTask->id, $task->name));
                }
            });

            $loserTaskIds = $taskLosers->pluck('id')->toArray();

            $task->userTasks()->whereIn('id', $winnerTaskIds)->update([
                'winner_by_invites' => true,
            ]);

            $task->userTasks()->whereIn('id', $loserTaskIds)->update([
                'status' => UserTask::STATUS_DONE,
            ]);
        });

        Task::whereIn('id', $tasks->pluck('id')->toArray())
            ->withoutGlobalScopes()
            ->update([
                'invite_contest_completed' => true,
            ]);

        return 1;
    }
}
