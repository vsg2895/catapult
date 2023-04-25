<?php

use App\Http\Controllers\{
    TagController,
    TaskController,
    AuthController,
    SkillController,
    TwitterController,
    ProjectController,
    CountryController,
    ProfileController,
    LanguageController,
    UserTaskController,
    ActivityController,
    CoinTypeController,
    DashboardController,
    InvitationController,
    UserWalletController,
    SocialLinkController,
    NewPasswordController,
    LeaderboardController,
    UserProjectController,
    NotificationController,
    VerificationController,
    ProjectDiscordController,
    SocialAuthenticationController,
    UserWalletWithdrawalRequestController,
};

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['api', 'json.response'])->group(function () {
    Route::get('/', function () {
        return response()->json([
            'ambassador' => 'api',
        ]);
    });

    Route::get('tags', [TagController::class, 'index']);
    Route::get('skills', [SkillController::class, 'index']);
    Route::get('countries', [CountryController::class, 'index']);
    Route::get('languages', [LanguageController::class, 'index']);
    Route::get('activities', [ActivityController::class, 'index']);
    Route::get('social-links', [SocialLinkController::class, 'index']);

    Route::prefix('invitations')->group(function () {
        Route::get('/verify/{invitation:token}', [InvitationController::class, 'verify']);
        Route::get('/accept/{invitation:token}', [InvitationController::class, 'accept']);
        Route::get('/revoke/{invitation:token}', [InvitationController::class, 'revoke']);
    });

    Route::middleware(['auth'])->group(function () {
        Route::prefix('tasks')->group(function () {
            Route::get('/{task}', [TaskController::class, 'show']);
            Route::get('take/{task}', [TaskController::class, 'take']);
            Route::get('/', [TaskController::class, 'index']);
        });

        Route::prefix('profile')->group(function () {
            Route::delete('delete', [ProfileController::class, 'delete']);
            Route::put('update', [ProfileController::class, 'update']);
        });

        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::post('/read', [NotificationController::class, 'read']);
        });

        Route::prefix('projects')->group(function () {
            Route::post('/{project}/report', [ProjectController::class, 'report']);

            Route::get('/{project}/discord/guild', [ProjectDiscordController::class, 'index']);
            Route::get('/{project}/discord/guild/roles', [ProjectDiscordController::class, 'roles']);

            Route::get('/', [ProjectController::class, 'index']);
            Route::get('/leaderboard', [ProjectController::class, 'leaderboard']);
            Route::get('/{refOrInvitationProject}', [ProjectController::class, 'show']);
            Route::get('/{project}/activities', [ProjectController::class, 'activities']);
        });

        Route::prefix('user-tasks')->group(function () {
            Route::get('/', [UserTaskController::class, 'index']);
            Route::get('/{userTask}', [UserTaskController::class, 'show']);
            Route::post('claim/{userTask}', [UserTaskController::class, 'claim']);
            Route::post('report/{userTask}', [UserTaskController::class, 'report']);
        });

        Route::prefix('user-wallets')->group(function () {
            Route::get('/', [UserWalletController::class, 'index']);
            Route::post('/', [UserWalletController::class, 'store']);
            Route::put('/{userWallet}', [UserWalletController::class, 'update']);
            Route::delete('/{userWallet}', [UserWalletController::class, 'destroy']);
            Route::get('history', [UserWalletController::class, 'history']);
            Route::get('total', [UserWalletController::class, 'total']);
            Route::get('withdrawal-requests', [UserWalletWithdrawalRequestController::class, 'index']);
            Route::post('withdrawal-requests', [UserWalletWithdrawalRequestController::class, 'store']);
        });

        Route::prefix('user-projects')->group(function () {
            Route::get('/', [UserProjectController::class, 'index']);
            Route::post('/{refOrInvitationProject}', [UserProjectController::class, 'store']);
            Route::delete('/{project}', [UserProjectController::class, 'destroy']);
        });

        Route::prefix('twitter')->group(function () {
            Route::get('user/{name}', [TwitterController::class, 'user']);
            Route::get('tweet/{id}', [TwitterController::class, 'tweet']);
            Route::get('space/{name}', [TwitterController::class, 'space']);
        });

        Route::prefix('dashboard')->group(function () {
            Route::get('/', [DashboardController::class, 'index']);
            Route::get('overview', [DashboardController::class, 'overview']);
        });

        Route::get('projects', [ProjectController::class, 'index']);
        Route::get('coin-types', [CoinTypeController::class, 'index']);
        Route::get('leaderboard', [LeaderboardController::class, 'index']);
        Route::get('leaderboard/project', [LeaderboardController::class, 'project']);
    });

    Route::prefix('auth')->group(function () {
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('verify-signature', [AuthController::class, 'verifySignature']);
        Route::post('refresh-password', [NewPasswordController::class, 'refresh']);
        Route::post('reset-password/{token}', [NewPasswordController::class, 'reset']);

        Route::get('/{provider}/callback', [SocialAuthenticationController::class, 'handleProviderCallback'])
            ->where(['provider' => 'discord|twitter|telegram']);

        Route::get('/{provider}/redirect', [SocialAuthenticationController::class, 'redirectProvider'])
            ->where(['provider' => 'discord|twitter|telegram']);

        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);

        Route::post('signature', [AuthController::class, 'signature']);
        Route::post('validate-name', [AuthController::class, 'validateName']);
        Route::post('validate-email', [AuthController::class, 'validateEmail']);

        Route::middleware('auth')->group(function () {
            Route::delete('/{provider}', [SocialAuthenticationController::class, 'destroy'])
                ->where(['provider' => 'discord|twitter|telegram']);

            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
            Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware('signed');
            Route::post('email/verification-notification', [VerificationController::class, 'resend'])->middleware('throttle:6,1');
        });
    });
});
