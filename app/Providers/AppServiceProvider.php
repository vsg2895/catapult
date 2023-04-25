<?php

namespace App\Providers;

use App\Services\DiscordService;
use App\Channels\DatabaseChannel;
use App\Contracts\DiscordServiceContract;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\{VerifyEmail, ResetPassword};
use Illuminate\Notifications\Channels\DatabaseChannel as BaseDatabaseChannel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->instance(BaseDatabaseChannel::class, new DatabaseChannel());
        $this->app->bind(DiscordServiceContract::class, DiscordService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(static function ($notifiable, $token) {
            return config('app.ambassador_frontend_url').'/update-password?'.http_build_query([
                'email' => $notifiable->getEmailForPasswordReset(),
                'token' => $token,
            ]);
        });

        VerifyEmail::createUrlUsing(static function ($notifiable) {
            return config('app.ambassador_frontend_url').'/verify-email/'.$notifiable->getKey().'/'.sha1($notifiable->getEmailForVerification());
        });
    }
}
