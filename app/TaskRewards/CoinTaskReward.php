<?php

namespace App\TaskRewards;

use Brick\Math\BigDecimal;
use App\Models\{User, Task};
use App\Contracts\TaskRewardContract;

class CoinTaskReward extends BaseTaskReward implements TaskRewardContract
{
    public function giveTo(User $user, Task $task)
    {
        $coins = BigDecimal::of($this->taskReward->value);
        if ($task->level_coefficient) {
            $coins = $coins->multipliedBy(config('levels.coefficients')[$user->level]);
        }

        $userWallet = $user->wallets()->firstOrCreate([
            'coin_type_id' => $task->coin_type_id,
        ], [
            'address' => '',
            'balance' => 0,
        ]);

        $userWallet->balance = (string) $coins->plus($userWallet->balance);
        $userWallet->save();

        $user->historyWallets()->create([
            'value' => (string) $coins,
            'points' => 5,
            'task_id' => $task->id,
            'user_wallet_id' => $userWallet->id,
        ]);
    }
}
