<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class TaskConditionServiceProvider extends ServiceProvider
{
    /**
     * The verifier mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected array $conditions = [
        'discord_role' => 'App\TaskConditions\DiscordRoleTaskCondition',
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerConditions();
    }

    /**
     * Register the application's verifiers.
     *
     * @return void
     */
    public function registerConditions(): void
    {
        foreach ($this->conditions as $key => $value) {
            $this->app->bind($key, $value);
        }
    }
}
