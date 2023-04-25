<?php

namespace App\Providers;

use App\Models\{
    Task,
    Project,
    UserTask,
    Invitation,
};

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        Route::bind('task', function ($value) {
            return Task::where('id', $value)
                ->forUser(ignoreUserTasks: true)
                ->firstOrFail();
        });

        Route::bind('refOrInvitationProject', function ($value) {
            $ref = request()->query->get('ref');
            if ($ref) {
                $userTask = UserTask::where('referral_code', $ref)
                    ->with([
                        'task' => fn ($query) => $query->withoutGlobalScopes(),
                        'task.project' => fn ($query) => $query->withoutGlobalScopes(),
                    ])
                    ->firstOrFail();

                return $userTask->task->project;
            }

            return Project::withoutGlobalScopes()
                ->whereHas('invitations', function ($query) use ($value) {
                    return $query->where('project_id', $value)
                        ->where('userable_id', auth()->id())
                        ->where('userable_type', 'App\Models\User');
                })
                ->orWhere(function ($query) use ($value) {
                    $query->where('id', $value)
                        ->where('public', true);
                })
                ->firstOrFail();
        });

        Route::bind('invitation', function ($value) {
            return Invitation::where('userable_type', 'App\Models\User')
                ->where('token', $value)
                ->firstOrFail();
        });

        $this->routes(function () {
            if (app()->environment('stage')) {
                Route::middleware('api')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/api.php'));
            } else {
                Route::prefix('api')
                    ->middleware('api')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/api.php'));
            }
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
