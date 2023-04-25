<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class TaskVerifierServiceProvider extends ServiceProvider
{
    /**
     * The verifier mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected array $verifiers = [
        'twitter_like' => 'App\TaskVerifiers\TwitterPostLikeTaskVerifier',
        'twitter_reply' => 'App\TaskVerifiers\TwitterPostReplyTaskVerifier',
        'twitter_tweet' => 'App\TaskVerifiers\TwitterPostTaskVerifier',
        'twitter_space' => 'App\TaskVerifiers\TwitterSpaceTaskVerifier',
        'twitter_follow' => 'App\TaskVerifiers\TwitterFollowTaskVerifier',
        'twitter_retweet' => 'App\TaskVerifiers\TwitterPostRetweetTaskVerifier',
        'discord_invite' => 'App\TaskVerifiers\DiscordInviteTaskVerifier',
        'telegram_invite' => 'App\TaskVerifiers\TelegramGroupJoinTaskVerifier',
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerVerifiers();
    }

    /**
     * Register the application's verifiers.
     *
     * @return void
     */
    public function registerVerifiers(): void
    {
        foreach ($this->verifiers as $key => $value) {
            $this->app->bind($key, $value);
        }
    }
}
