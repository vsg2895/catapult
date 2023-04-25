<?php

namespace App\Console\Commands;

use App\Models\UserTask;
use App\Models\Invitation;
use Illuminate\Console\Command;

class RemoveInactiveInvitesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invites:remove-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove inactive invitations after week';

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
        $invitations = Invitation::where('status', Invitation::STATUS_PENDING)
            ->where('created_at', '<=', now()->subDays(7))
            ->get();

        Invitation::whereIn('id', $invitations->pluck('id')->toArray())
            ->update(['status' => Invitation::STATUS_EXPIRED]);

        return 1;
    }
}
