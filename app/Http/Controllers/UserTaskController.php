<?php

namespace App\Http\Controllers;

use App\Models\{AmbassadorTask, Task, Manager, UserTask};

use App\Http\Requests\{
    UserTaskReportRequest,
    UserTaskListRequest,
};

use App\Http\Resources\{
    UserTaskCollection,
    UserTask as UserTaskResource,
};

use App\Notifications\{
    NewTaskOnReviewNotification,
    UserTaskAfterRevisionNotification,
    Social\UserTaskCompletedSocialNotification,
};

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserTaskController extends Controller
{
    /**
     * Get List Active User Tasks
     * @OA\Get (
     *     path="/api/user-tasks",
     *     tags={"User Tasks"},
     *     @OA\Parameter(
     *         in="query",
     *         name="page",
     *         required=false,
     *         @OA\Schema(type="number"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="per_page",
     *         required=false,
     *         @OA\Schema(type="number"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="search",
     *         required=false,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="status",
     *         required=false,
     *         description="Status of tasks",
     *         @OA\Schema(type="string", enum={"done", "rejected", "overdue", "returned", "in_progress", "waiting_for_review"}, example={"done", "returned", "in_progress", "waiting_for_review"}),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="order_by_deadline",
     *         required=true,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example={"asc", "desc"}),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *              @OA\Property(
     *                  property="id",
     *                  type="number",
     *                  example="1",
     *              ),
     *              @OA\Property(
     *                  property="user",
     *                  @OA\Property(
     *                      property="id",
     *                      type="number",
     *                      example="1",
     *                  ),
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example="example name",
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="task",
     *                  @OA\Property(
     *                      property="id",
     *                      type="number",
     *                      example="1",
     *                  ),
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example="example name"
     *                  ),
     *                  @OA\Property(
     *                      property="text",
     *                      type="string",
     *                      example="example text",
     *                  ),
     *                  @OA\Property(
     *                      property="rewards",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(
     *                              property="type",
     *                              type="string",
     *                              enum={"coins", "discord_role"},
     *                          ),
     *                          @OA\Property(
     *                              property="value",
     *                              type="string",
     *                          ),
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                      property="conditions",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(
     *                              property="type",
     *                              type="string",
     *                              enum={"discord_role"},
     *                          ),
     *                          @OA\Property(
     *                              property="value",
     *                              type="string",
     *                          ),
     *                          @OA\Property(
     *                              property="operator",
     *                              type="string",
     *                              enum={"="},
     *                          ),
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                      property="project",
     *                      nullable=false,
     *                      @OA\Property(property="id", type="number", example="1"),
     *                      @OA\Property(property="name", type="string", example="example name"),
     *                  ),
     *                  @OA\Property(
     *                      property="activity",
     *                      nullable=true,
     *                      @OA\Property(property="id", type="number", example="1"),
     *                      @OA\Property(property="name", type="string", example="example name"),
     *                  ),
     *                  @OA\Property(
     *                      property="priority",
     *                      enum={"low", "middle", "high"},
     *                  ),
     *                  @OA\Property(
     *                      property="coin_type",
     *                      nullable=true,
     *                      @OA\Property(property="id", type="number", example="1"),
     *                      @OA\Property(property="name", type="string", example="example name"),
     *                      @OA\Property(property="type_of_chain", type="string", example="example name"),
     *                  ),
     *                  @OA\Property(
     *                      property="autovalidate",
     *                      type="boolean",
     *                      default="false",
     *                  ),
     *                  @OA\Property(
     *                      property="verifier_driver",
     *                      enum={"twitter", "telegram", "discord"},
     *                      nullable=true,
     *                  ),
     *                  @OA\Property(
     *                      property="started_at",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="ended_at",
     *                      type="string",
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="report",
     *                  nullable=true,
     *                  type="string",
     *                  example="example report",
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  enum={"done", "rejected", "overdue", "returned", "in_progress", "waiting_for_review"},
     *                  example="in_progress",
     *              ),
     *              @OA\Property(
     *                  property="user_id",
     *                  type="number",
     *                  example="1",
     *              ),
     *              @OA\Property(
     *                  property="task_id",
     *                  type="number",
     *                  example="1",
     *              ),
     *              @OA\Property(
     *                  property="reported_at",
     *                  nullable=true,
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="latest_return_comment",
     *                  nullable=true,
     *                  type="string",
     *                  example="example return comment",
     *              ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated."),
     *         ),
     *     ),
     *     security={{ "apiAuth": {} }},
     * )
     */
    public function index(UserTaskListRequest $request)
    {
        $search = '%'.$request->get('search').'%';
        $userTasks = auth()->user()->tasks();

        if ($request->has('status')) {
            $userTasks->where('status', $request->get('status'));
        }

        $userTasks->with([
            'user',
            'task' => fn ($query) => $query->withoutGlobalScopes(),
            'task.project' => fn ($query) => $query->withoutGlobalScopes(),
            'task.rewards',
            'task.activity',
            'task.coinType',
            'latestComments',
        ])->orderBy(
            Task::select('ended_at')
                ->whereColumn('tasks.id', 'user_tasks.id')
                ->orderBy('ended_at', $request->get('order_by_deadline'))
                ->limit(1)
        );

        if ($request->has('search')) {
            $userTasks->whereRelation('user', 'name', 'LIKE', $search)
                ->orWhereRelation('task', 'name', 'LIKE', $search)
                ->orWhereRelation('task.project', 'name', 'LIKE', $search);
        }

        $userTasks = $userTasks->paginate($request->get('per_page') ?: 10);
        return response()->json(new UserTaskCollection($userTasks));
    }

    /**
     * Get user task
     * @OA\Get (
     *     path="/api/user-tasks/{userTask}",
     *     tags={"User Tasks"},
     *     @OA\Parameter(
     *         in="path",
     *         name="userTask",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *              @OA\Property(
     *                  property="id",
     *                  type="number",
     *                  example="1",
     *              ),
     *              @OA\Property(
     *                  property="user",
     *                  @OA\Property(
     *                      property="id",
     *                      type="number",
     *                      example="1",
     *                  ),
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example="example name",
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="task",
     *                  @OA\Property(
     *                      property="id",
     *                      type="number",
     *                      example="1",
     *                  ),
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example="example name"
     *                  ),
     *                  @OA\Property(
     *                      property="text",
     *                      type="string",
     *                      example="example text",
     *                  ),
     *                  @OA\Property(
     *                      property="coins",
     *                      type="number",
     *                      example="1",
     *                  ),
     *                  @OA\Property(
     *                      property="project",
     *                      nullable=false,
     *                      @OA\Property(property="id", type="number", example="1"),
     *                      @OA\Property(property="name", type="string", example="example name"),
     *                  ),
     *                  @OA\Property(
     *                      property="activity",
     *                      nullable=true,
     *                      @OA\Property(property="id", type="number", example="1"),
     *                      @OA\Property(property="name", type="string", example="example name"),
     *                  ),
     *                  @OA\Property(
     *                      property="priority",
     *                      type="string",
     *                      enum={"low", "middle", "high"},
     *                  ),
     *                  @OA\Property(
     *                      property="coin_type",
     *                      nullable=true,
     *                      @OA\Property(property="id", type="number", example="1"),
     *                      @OA\Property(property="name", type="string", example="example name"),
     *                      @OA\Property(property="type_of_chain", type="string", example="example name"),
     *                  ),
     *                  @OA\Property(
     *                      property="autovalidate",
     *                      type="boolean",
     *                      default="false",
     *                  ),
     *                  @OA\Property(
     *                      property="verifier_driver",
     *                      type="string",
     *                      enum={"twitter", "telegram", "discord"},
     *                      nullable=true,
     *                  ),
     *                  @OA\Property(
     *                      property="started_at",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="ended_at",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="verifier",
     *                      nullable=true,
     *                      @OA\Property(property="id", type="string", example="1"),
     *                      @OA\Property(
     *                          property="types",
     *                          type="array",
     *                          @OA\Items(type="string", enum={
     *                              "discord_invite",
     *                              "telegram_invite",
     *                              "twitter_like",
     *                              "twitter_tweet",
     *                              "twitter_reply",
     *                              "twitter_space",
     *                              "twitter_follow",
     *                              "twitter_retweet",
     *                          }),
     *                      ),
     *                      @OA\Property(property="invite_link", type="string", nullable=true),
     *                      @OA\Property(property="twitter_tweet", type="string", nullable=true),
     *                      @OA\Property(property="twitter_space", type="string", nullable=true),
     *                      @OA\Property(property="twitter_follow", type="string", nullable=true),
     *                      @OA\Property(property="default_reply", type="string", nullable=true),
     *                      @OA\Property(property="default_tweet", type="string", nullable=true),
     *                      @OA\Property(
     *                          property="tweet_words",
     *                          type="array",
     *                          @OA\Items(type="string"),
     *                      ),
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="report",
     *                  nullable=true,
     *                  type="string",
     *                  example="example report",
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  enum={"done", "rejected",  "overdue", "returned", "in_progress", "waiting_for_review"},
     *                  example="in_progress",
     *              ),
     *              @OA\Property(
     *                  property="user_id",
     *                  type="number",
     *                  example="1",
     *              ),
     *              @OA\Property(
     *                  property="task_id",
     *                  type="number",
     *                  example="1",
     *              ),
     *              @OA\Property(
     *                  property="invites",
     *                  nullable=true,
     *                  @OA\Property(property="code", type="string"),
     *                  @OA\Property(property="count", type="number", example="0"),
     *                  @OA\Property(property="winner_of_contest", type="boolean", example="false"),
     *              ),
     *              @OA\Property(
     *                  property="reported_at",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="latest_return_comment",
     *                  nullable=true,
     *                  type="string",
     *                  example="example return comment",
     *              ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="errors",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated."),
     *         ),
     *     ),
     *     security={{ "apiAuth": {} }},
     * )
     */
    public function show(UserTask $userTask)
    {
        $userTask->load([
            'media',
            'task' => fn ($query) => $query->withoutGlobalScopes(),
            'task.verifier',
            'task.media',
            'task.project' => fn ($query) => $query->withoutGlobalScopes(),
            'task.activity',
            'task.coinType',
            'task.rewards',
            'task.conditions',
            'latestComments',
        ]);

        if ($userTask->task->is_invite_friends) {
            $userTask->loadCount(['referrals']);
        }

        return response()->json(new UserTaskResource($userTask));
    }

    /**
     * User task report
     * @OA\Post (
     *     path="/api/user-tasks/report/{userTask}",
     *     tags={"User Tasks"},
     *     @OA\Parameter(
     *         in="path",
     *         name="task",
     *         required=true,
     *         @OA\Schema(type="number"),
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="text",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="files[]",
     *                      type="array",
     *                      @OA\Items(type="string", format="binary"),
     *                  ),
     *             ),
     *         ),
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *              @OA\Property(
     *                  property="id",
     *                  type="number",
     *                  example="1",
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  enum={"done", "rejected", "overdue", "returned", "in_progress", "waiting_for_review"},
     *                  example="in_progress",
     *              ),
     *              @OA\Property(
     *                  property="user_id",
     *                  type="number",
     *                  example="1",
     *              ),
     *              @OA\Property(
     *                  property="task_id",
     *                  type="number",
     *                  example="1",
     *              ),
     *              @OA\Property(
     *                  property="reported_at",
     *                  type="string",
     *              ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="invalid",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Can't report this user task!"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated."),
     *         ),
     *     ),
     *     security={{ "apiAuth": {} }},
     * )
     */
    public function report(UserTask $userTask, UserTaskReportRequest $request)
    {
        if ($userTask->task->autovalidate || $userTask->task->is_invite_friends || in_array($userTask->status, [
            UserTask::STATUS_DONE,
            UserTask::STATUS_ON_REVISION,
            UserTask::STATUS_WAITING_FOR_REVIEW,
        ], true)) {
            return response()->json([
                'message' => 'Can\'t report this user task!',
            ], 400);
        }

        $user = auth()->user();
        $report = $request->get('text');
        $reportedAt = now();

        if ($userTask->manager_id) {
            $userTask->update([
                'report' => $report,
                'status' => UserTask::STATUS_ON_REVISION,
                'manager_id' => $userTask->manager_id,
                'reported_at' => $reportedAt,
            ]);

            $userName = $user->name;
            dispatch(static function () use ($userName, $userTask) {
                $superAdmins = Manager::where('id', '!=', $userTask->manager_id)
                    ->whereHas('allRoles', static function ($query) {
                        $query->where('name', 'Super Admin');
                    })
                    ->get();

                $project = $userTask->task->project;
                $superAdmins->each->notify(new UserTaskAfterRevisionNotification($userName, $userTask));

                optional($project->owner)->notify(new UserTaskAfterRevisionNotification($userName, $userTask));

                if ($userTask->manager_id !== $project->owner_id) {
                    $userTask->manager->notify(new UserTaskAfterRevisionNotification($userName, $userTask));
                }
            })->afterResponse();
        } else {
            $userTask->update([
                'report' => $report,
                'status' => UserTask::STATUS_WAITING_FOR_REVIEW,
                'reported_at' => $reportedAt,
            ]);

            dispatch(static function () use ($user, $userTask) {
                $managers = Manager::whereHas('allRoles', static function ($query) use ($userTask) {
                    $query->where('model_has_roles.team_id', $userTask->task->project_id)
                        ->orWhere('name', 'Super Admin');
                })->get();

                $managers->each->notify(new NewTaskOnReviewNotification($user->name, $userTask));
            })->afterResponse();
        }

        if ($request->has('files')) {
            $userTask->addMultipleMediaFromRequest(['files'])->each->toMediaCollection();
        }

        return response()->json($userTask);
    }

    /**
     * User task claim
     * @OA\Post (
     *     path="/api/user-tasks/claim/{userTask}",
     *     tags={"User Tasks"},
     *     @OA\Parameter(
     *         in="path",
     *         name="task",
     *         required=true,
     *         @OA\Schema(type="number"),
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="user_tweet_id",
     *                      type="number",
     *                  ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="no content",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="invalid",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated."),
     *         ),
     *     ),
     *     security={{ "apiAuth": {} }},
     * )
     * @throws Exception
     */
    public function claim(Request $request, UserTask $userTask)
    {
        $userTask->load(['task', 'task.rewards']);

        if ($userTask->status !== UserTask::STATUS_IN_PROGRESS
            || (!$userTask->task->autovalidate && !$userTask->task->is_invite_friends)) {
            return response()->json([
                'message' => 'Can\'t claim this user task!',
            ], 400);
        }

        $user = auth()->user();
        $task = $userTask->task;

        // TODO: Task checker?
        if ($task->is_invite_friends) {
            $userTask->loadCount(['referrals']);

            if ($task->number_of_winners > 0 && !$userTask->winner_by_invites) {
                return response()->json([
                    'message' => 'You are not the winner of the contest!',
                ], 400);
            }

            if ($task->number_of_invites > 0 && $task->number_of_invites > $userTask->referrals_count) {
                return response()->json([
                    'message' => 'Not enough invites!',
                ], 400);
            }
        } else {
            $userTask->load(['task.verifier']);

            $verifierDriver = $task->verifier_driver;
            $socialProvider = $user->socialProviders()->where('provider_name', $verifierDriver)->first();

            if (!$socialProvider) {
                return response()->json([
                    'message' => "User without $verifierDriver social provider",
                ], 400);
            }

            $results = [];

            if ($task->verifier_driver === 'twitter') {
                sleep(3); // delay for correct twitter results
            }

            foreach ($task->verifier->types as $type) {
                $results[$type] = app($type, ['socialProvider' => $socialProvider])->verify($task);
            }

            if (collect($results)->values()->contains(false)) {
                return response()->json(compact('results'), 400);
            }
        }

        DB::beginTransaction();

        try {
            // FIXME: Fire this actions on listeners or model events
            foreach ($task->rewards as $reward) {
                app($reward->type, ['taskReward' => $reward])->giveTo($user, $task);
            }

            $rating = 5;
            $projectId = $task->project_id;

            $user->update([
                'points' => DB::raw("points + $rating"),
                'total_points' => DB::raw("total_points + $rating"),
            ]);

            $levelPoints = $user->levelPoints()->firstOrCreate([
                'level' => $user->level,
                'project_id' => $projectId,
                'activity_id' => $task->activity_id,
            ], [
                'points' => 0,
            ]);

            $levelPoints->increment('points', $rating);

            $userTask->update([
                'report' => $request->input('user_tweet'),
                'status' => UserTask::STATUS_DONE,
                'rating' => $rating,
                'completed_at' => now(),
            ]);

            // FIXME: Move to event? Best conditions? Rework logic?
            if ($user->tasksIsDone()->whereRelation('task', 'project_id', $task->project_id)->count() === 1) {
                $userProject = $user->projectMembers()->where('project_id', $projectId)
                    ->whereNotNull('referral_code')
                    ->first();

                if ($userProject) {
                    $userTaskByReferralCode = UserTask::firstWhere('referral_code', $userProject->referral_code);
                    $userTaskByReferralCode?->referrals()->create([
                        'user_id' => $userTaskByReferralCode->user_id,
                        'task_id' => $userTaskByReferralCode->task_id,
                        'referral_id' => $user->id,
                    ]);
                }
            }

            $task->project->notify(new UserTaskCompletedSocialNotification($userTask, $user->name));

            DB::commit();
            return response()->noContent();
        } catch (Exception $error) {
            DB::rollBack();
            throw $error;
        }
    }
}
