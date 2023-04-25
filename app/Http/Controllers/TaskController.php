<?php

namespace App\Http\Controllers;

use App\Models\{
    Task,
    Project,
    UserTask,
    ProjectMember,
};

use App\Http\Resources\{
    Task as TaskResource,
    TaskProjectCollection,
};

use App\Http\Requests\TaskListRequest;

use Illuminate\Support\Str;

class TaskController extends Controller
{
    /**
     * Get Task
     * @OA\Get (
     *     path="/api/tasks/{task}",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         in="path",
     *         name="task",
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
     *                  property="name",
     *                  type="string",
     *                  example="example name"
     *              ),
     *              @OA\Property(
     *                  property="text",
     *                  type="string",
     *                  example="example text",
     *              ),
     *              @OA\Property(
     *                  property="rewards",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="type",
     *                          type="string",
     *                          enum={"coins", "discord_role"},
     *                      ),
     *                      @OA\Property(
     *                          property="value",
     *                          type="string",
     *                      ),
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="conditions",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="type",
     *                          type="string",
     *                          enum={"discord_role"},
     *                      ),
     *                      @OA\Property(
     *                          property="value",
     *                          type="string",
     *                      ),
     *                      @OA\Property(
     *                          property="operator",
     *                          type="string",
     *                          enum={"="},
     *                      ),
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="project",
     *                  nullable=false,
     *                  @OA\Property(property="id", type="number", example="1"),
     *                  @OA\Property(property="name", type="string", example="example name"),
     *              ),
     *              @OA\Property(
     *                  property="activity",
     *                  nullable=true,
     *                  @OA\Property(property="id", type="number", example="1"),
     *                  @OA\Property(property="name", type="string", example="example name"),
     *              ),
     *              @OA\Property(
     *                  property="priority",
     *                  type="string",
     *                  enum={"low", "middle", "high"},
     *              ),
     *              @OA\Property(
     *                  property="coin_type",
     *                  nullable=true,
     *                  @OA\Property(property="id", type="number", example="1"),
     *                  @OA\Property(property="name", type="string", example="example name"),
     *                  @OA\Property(property="type_of_chain", type="string", example="example name"),
     *              ),
     *              @OA\Property(
     *                  property="autovalidate",
     *                  type="boolean",
     *                  default="false",
     *              ),
     *              @OA\Property(
     *                  property="verifier_driver",
     *                  type="string",
     *                  enum={"twitter", "telegram", "discord"},
     *                  nullable=true,
     *              ),
     *              @OA\Property(
     *                  property="verifier",
     *                  nullable=true,
     *                  @OA\Property(property="id", type="string", example="1"),
     *                  @OA\Property(
     *                      property="types",
     *                      type="array",
     *                      @OA\Items(type="string", enum={
     *                          "discord_invite",
     *                          "telegram_invite",
     *                          "twitter_like",
     *                          "twitter_tweet",
     *                          "twitter_reply",
     *                          "twitter_space",
     *                          "twitter_follow",
     *                          "twitter_retweet",
     *                      }),
     *                  ),
     *                  @OA\Property(property="invite_link", type="string", nullable=true),
     *                  @OA\Property(property="twitter_tweet", type="string", nullable=true),
     *                  @OA\Property(property="twitter_space", type="string", nullable=true),
     *                  @OA\Property(property="twitter_follow", type="string", nullable=true),
     *                  @OA\Property(property="default_reply", type="string", nullable=true),
     *                  @OA\Property(property="default_tweet", type="string", nullable=true),
     *                  @OA\Property(
     *                      property="tweet_words",
     *                      type="array",
     *                      @OA\Items(type="string"),
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="started_at",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="ended_at",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="number_of_invites",
     *                  type="number",
     *                  example="0",
     *              ),
     *              @OA\Property(
     *                  property="is_invite_friends",
     *                  type="boolean",
     *                  example="false",
     *              ),
     *              @OA\Property(
     *                  property="status_by_dates",
     *                  type="string",
     *                  enum={"available", "upcoming", "finished"},
     *                  example="available",
     *              ),
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
    public function show(Task $task)
    {
        $task->load([
            'media',
            'conditions',
            'rewards',
            'verifier',
            'project',
            'activity',
            'coinType',
            'inWorkByUser',
            'project.socialProviders',
        ]);

        if ($task->conditions->pluck('is_opened')->contains(false)) {
            return response()->json([
                'message' => 'Task not available by conditions!',
            ], 404);
        }

        return response()->json(new TaskResource($task));
    }

    /**
     * Get List Tasks
     * @OA\Get (
     *     path="/api/tasks",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         in="query",
     *         name="search",
     *         required=false,
     *         @OA\Schema(type="string"),
     *     ),
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
     *         name="status",
     *         required=false,
     *         description="Status of tasks",
     *         @OA\Schema(type="string", enum={"available", "upcoming"}),
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
     *                  property="name",
     *                  type="string",
     *                  example="example name"
     *              ),
     *              @OA\Property(
     *                  property="text",
     *                  type="string",
     *                  example="example text",
     *              ),
     *              @OA\Property(
     *                  property="project",
     *                  nullable=false,
     *                  @OA\Property(property="id", type="number", example="1"),
     *                  @OA\Property(property="name", type="string", example="example name"),
     *              ),
     *              @OA\Property(
     *                  property="activity",
     *                  nullable=true,
     *                  @OA\Property(property="id", type="number", example="1"),
     *                  @OA\Property(property="name", type="string", example="example name"),
     *              ),
     *              @OA\Property(
     *                  property="priority",
     *                  type="string",
     *                  enum={"low", "middle", "high"},
     *              ),
     *              @OA\Property(
     *                  property="coin_type",
     *                  nullable=true,
     *                  @OA\Property(property="id", type="number", example="1"),
     *                  @OA\Property(property="name", type="string", example="example name"),
     *                  @OA\Property(property="type_of_chain", type="string", example="example name"),
     *              ),
     *              @OA\Property(
     *                  property="autovalidate",
     *                  type="boolean",
     *                  default="false",
     *              ),
     *              @OA\Property(
     *                  property="verifier_driver",
     *                  type="string",
     *                  enum={"twitter", "telegram", "discord"},
     *                  nullable=true,
     *              ),
     *              @OA\Property(
     *                  property="started_at",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="ended_at",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="status_by_dates",
     *                  type="string",
     *                  enum={"available", "upcoming", "finished"},
     *                  example="available",
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
    public function index(TaskListRequest $request)
    {
        $status = $request->get('status', 'all');
        $search = '%'.$request->get('search').'%';
        $hasSearch = $request->has('search');

        $searchFunction = function ($query) use ($status, $search, $hasSearch) {
            if ($hasSearch) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', $search)
                        ->orWhereRelation('project', 'name', 'LIKE', $search);
                });
            }

            $now = now();
            if ($status === 'upcoming') {
                $query->whereDate('started_at', '>', $now);
            } else if ($status === 'available') {
                $query->where(function ($query) use ($now) {
                    $query->whereDate('started_at', '<=', $now)
                        ->whereDate('ended_at', '>', $now->subDays(1));
                });
            } else {
                $query->whereDate('ended_at', '>', $now->subDays(1));
            }

            $query->whereRaw('tasks.number_of_participants = 0 OR tasks.number_of_participants > (SELECT COUNT(*) FROM user_tasks WHERE user_tasks.task_id = tasks.id)');
        };

        $projects = Project::when($request->has('search'), function ($query) use ($search) {
            $query->where('name', 'LIKE', $search);
        })->with([
            'tasksForUser' => $searchFunction,
            'tasksForUser.rewards',
            'tasksForUser.activity',
            'tasksForUser.coinType',
            'tasksForUser.conditions',
        ])->orWhereHas('tasksForUser', $searchFunction);

        $projects = $projects->get();
        foreach ($projects as $project) {
            $project->setRelation('tasksForUser', $project->tasksForUser->filter(function ($task) {
                return !$task->conditions->pluck('is_opened')->contains(false);
            }));
        }

        return response()->json(new TaskProjectCollection($projects));
    }

    /**
     * Take task
     * @OA\Get (
     *     path="/api/tasks/take/{task}",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         in="path",
     *         name="task",
     *         required=true,
     *         @OA\Schema(type="number"),
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
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="invalid",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  @OA\Examples(value="Can't take this task to work!"),
     *                  @OA\Examples(value="Maximum users in this task!"),
     *                  @OA\Examples(value="You have maximum tasks in work!"),
     *             ),
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
    public function take(Task $task)
    {
        $user = auth()->user();
        $assignedOrHasActivity = !$task->activity_id
            || $user->activities()->active()->pluck('activity_id')->contains($task->activity_id)
            || $task->userAssignments()->pluck('user_id')->contains($user->id);

        $task->load(['users']);

        if ($task->status_by_dates !== 'available'
            || !$assignedOrHasActivity
            || $task->users->pluck('user_id')->contains($user->id)
            || $task->conditions->pluck('is_opened')->contains(false)) {
            return response()->json([
                'message' => 'Can\'t take this task to work!',
            ], 400);
        }

        if ($task->number_of_participants > 0 && $task->users->count() === $task->number_of_participants) {
            return response()->json([
                'message' => 'Maximum users in this task!',
            ], 400);
        }

        $user->loadCount(['tasksInWork']);
        if ($user->tasks_in_work_count >= config('app.maximum_tasks_in_work')) {
            return response()->json([
                'message' => 'You have maximum tasks in work!',
            ], 400);
        }

        $data = [
            'status' => UserTask::STATUS_IN_PROGRESS,
            'user_id' => $user->id,
            'task_id' => $task->id,
        ];

        if ($task->is_invite_friends) {
            $data['referral_code'] = Str::uuid();
        }

        $userTask = UserTask::create($data);

        $user->projectMembers()->firstOrCreate([
            'project_id' => $task->project_id,
        ], [
            'status' => ProjectMember::STATUS_ACCEPTED,
            'project_id' => $task->project_id,
        ]);

        return response()->json($userTask);
    }
}
