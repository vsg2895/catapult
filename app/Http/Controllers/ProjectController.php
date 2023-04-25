<?php

namespace App\Http\Controllers;

use App\Models\{Task, Project, UserTask};
use App\Http\Requests\{
    ProjectListRequest,
    UserProjectReportStoreRequest,
};

use App\Http\Resources\Project as ProjectResource;

use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Report project
     * @OA\Post (
     *     path="/api/projects/{project}/report",
     *     @OA\Parameter(
     *         in="path",
     *         name="project",
     *         required=true,
     *         @OA\Schema(type="number"),
     *     ),
     *     tags={"Projects"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="type",
     *                      type="string",
     *                      enum={"Fake or spam", "Technical issues", "Something else"},
     *                  ),
     *                  @OA\Property(
     *                      property="text",
     *                      type="string",
     *                  ),
     *             ),
     *         ),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="no content",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="errors",
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="invalid",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="You have reported this project!"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthorized"),
     *          ),
     *      ),
     *      security={{ "apiAuth": {} }},
     * )
     */
    public function report(Project $project, UserProjectReportStoreRequest $request)
    {
        $user = auth()->user();
        $projectReport = $user->projectReports()->where('project_id', $project->id)
            ->whereRaw('date_add(`created_at`, interval 7 day) >= CURDATE()')
            ->first();

        if ($projectReport) {
            return response()->json([
                'message' => 'You have reported this project!',
            ], 400);
        }

        $user->projectReports()->create($request->validated() + ['project_id' => $project->id]);
        return response()->noContent();
    }

    /**
     * Get List Projects
     * @OA\Get (
     *     path="/api/projects",
     *     tags={"Projects"},
     *     @OA\Parameter(
     *         in="query",
     *         name="page",
     *         required=false,
     *         @OA\Schema(type="number"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="tag_ids",
     *         required=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="number"),
     *         ),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="search",
     *         required=false,
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
     *                  property="logo",
     *                  type="string",
     *                  example="cdn.com/logo.png"
     *              ),
     *              @OA\Property(
     *                  property="banner",
     *                  type="string",
     *                  example="cdn.com/banner.png"
     *              ),
     *              @OA\Property(
     *                  property="description",
     *                  type="string",
     *                  example="example description"
     *              ),
     *              @OA\Property(
     *                  property="pool_amount",
     *                  type="number",
     *                  example="100"
     *              ),
     *              @OA\Property(
     *                  property="medium_username",
     *                  type="string",
     *                  example="@username"
     *              ),
     *              @OA\Property(
     *                  property="tags",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="number", example="1"),
     *                      @OA\Property(property="content", type="number", example="example.com"),
     *                      @OA\Property(
     *                          property="tag",
     *                          @OA\Property(property="id", type="number", example="1"),
     *                          @OA\Property(property="name", type="string", example="Telegram"),
     *                      ),
     *                  ),
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
    public function index(ProjectListRequest $request)
    {
        $tagIds = $request->get('tag_ids');
        $search = '%'.$request->get('search').'%';

        $projects = Project::with([
            'media',
            'tags',
            'tags.tag',
        ])->when($request->has('tag_ids'), function ($query) use ($tagIds) {
            $query->whereHas('tags', function ($query) use ($tagIds) {
                $query->whereIn('tag_id', $tagIds);
            });
        })->when($request->has('search'), function ($query) use ($search) {
           $query->where('name', 'LIKE', $search);
        })->get();

        return response()->json(ProjectResource::collection($projects));
    }

    /**
     * Get Project
     * @OA\Get (
     *     path="/api/projects/{project}",
     *     @OA\Parameter(
     *         in="path",
     *         name="project",
     *         required=true,
     *         @OA\Schema(type="number"),
     *     ),
     *     tags={"Projects"},
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
     *                  property="logo",
     *                  type="string",
     *                  example="cdn.com/logo.png"
     *              ),
     *              @OA\Property(
     *                  property="banner",
     *                  type="string",
     *                  example="cdn.com/banner.png"
     *              ),
     *              @OA\Property(
     *                  property="description",
     *                  type="string",
     *                  example="example description"
     *              ),
     *              @OA\Property(
     *                  property="pool_amount",
     *                  type="number",
     *                  example="100"
     *              ),
     *              @OA\Property(
     *                  property="reported",
     *                  type="boolean",
     *                  example="false",
     *              ),
     *              @OA\Property(
     *                  property="medium_username",
     *                  type="string",
     *                  example="@username"
     *              ),
     *              @OA\Property(
     *                  property="coin_type",
     *                  nullable=true,
     *                  @OA\Property(property="id", type="number", example="1"),
     *                  @OA\Property(property="name", type="string", example="example name"),
     *              ),
     *              @OA\Property(
     *                  property="blockchain",
     *                  nullable=true,
     *                  @OA\Property(property="id", type="number", example="1"),
     *                  @OA\Property(property="name", type="string", example="example name"),
     *              ),
     *              @OA\Property(
     *                  property="tags",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="number", example="1"),
     *                      @OA\Property(property="content", type="number", example="example.com"),
     *                      @OA\Property(
     *                          property="tag",
     *                          @OA\Property(property="id", type="number", example="1"),
     *                          @OA\Property(property="name", type="string", example="Telegram"),
     *                      ),
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="social_links",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="number", example="1"),
     *                      @OA\Property(property="content", type="number", example="example.com"),
     *                      @OA\Property(
     *                          property="social_link",
     *                          @OA\Property(property="id", type="number", example="1"),
     *                          @OA\Property(property="name", type="string", example="Telegram"),
     *                          @OA\Property(property="icon", type="string", example="cnd.com/telegram.png", nullable=true),
     *                      ),
     *                  ),
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
     *             @OA\Property(property="error", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *     security={{ "apiAuth": {} }},
     * )
     */
    public function show(Project $project)
    {
        $project->load([
            'media',
            'tags',
            'tags.tag',
            'coinType',
            'blockchain',
            'socialLinks',
            'showcaseTasks',
            'showcaseTasks.userTasksInWork',
            'showcaseTasks.userTasksInWork.user',
            'socialLinks.link',
            'socialLinks.link.media',
        ]);

        $project->reported = (bool) optional(DB::selectOne('SELECT EXISTS(SELECT 1 FROM `user_project_reports` WHERE `user_project_reports`.`user_id` = ? AND `user_project_reports`.`project_id` = ? AND date_add(`user_project_reports`.`created_at`, interval 7 day) >= CURDATE()) as reported', [
            auth()->id(),
            $project->id,
        ]))->reported;

        return response()->json(new ProjectResource($project));
    }

    /**
     * Get Project Activities
     * @OA\Get (
     *     path="/api/projects/{project}/activities",
     *     @OA\Parameter(
     *         in="path",
     *         name="project",
     *         required=true,
     *         @OA\Schema(type="number"),
     *     ),
     *     tags={"Projects"},
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
     *             @OA\Property(property="error", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *     security={{ "apiAuth": {} }},
     * )
     */
    public function activities(Project $project)
    {
        $activities = DB::table('user_tasks')
            ->select(['activities.id', 'activities.name'])
            ->where([
                ['user_tasks.status', '=', UserTask::STATUS_DONE],
                ['tasks.project_id', '=', $project->id],
                ['tasks.activity_id', '!=', null],
            ])
            ->leftJoin('tasks', 'user_tasks.task_id', '=', 'tasks.id')
            ->leftJoin('activities', 'tasks.activity_id', '=', 'activities.id')
            ->groupBy('activities.id')
            ->get();

        return response()->json($activities);
    }

    /**
     * Leaderboard Projects
     * @OA\Get (
     *     path="/api/projects/leaderboard",
     *     tags={"Projects"},
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
     *                  property="logo",
     *                  type="string",
     *                  example="cdn.com/logo.png"
     *              ),
     *              @OA\Property(
     *                  property="banner",
     *                  type="string",
     *                  example="cdn.com/banner.png"
     *              ),
     *              @OA\Property(
     *                  property="description",
     *                  type="string",
     *                  example="example description"
     *              ),
     *              @OA\Property(
     *                  property="pool_amount",
     *                  type="number",
     *                  example="100"
     *              ),
     *              @OA\Property(
     *                  property="medium_username",
     *                  type="string",
     *                  example="@username"
     *              ),
     *              @OA\Property(
     *                  property="tags",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="number", example="1"),
     *                      @OA\Property(property="content", type="number", example="example.com"),
     *                      @OA\Property(
     *                          property="tag",
     *                          @OA\Property(property="id", type="number", example="1"),
     *                          @OA\Property(property="name", type="string", example="Telegram"),
     *                      ),
     *                  ),
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
    public function leaderboard()
    {
        $projects = Project::with([
            'media',
            'tags',
            'tags.tag',
        ])->limit(5)->orderByDesc(
            Task::select('created_at')
                ->whereColumn('tasks.project_id', 'projects.id')
                ->orderByDesc('created_at')
                ->limit(1)
        )->get();

        return response()->json(ProjectResource::collection($projects));
    }
}
