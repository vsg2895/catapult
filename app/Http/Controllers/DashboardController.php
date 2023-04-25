<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard
     * @OA\Get (
     *     path="/api/dashboard",
     *     tags={"Dashboard"},
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="tasks",
     *                  @OA\Property(property="total", type="number"),
     *                  @OA\Property(property="upcoming", type="number"),
     *                  @OA\Property(property="available", type="number"),
     *              ),
     *              @OA\Property(
     *                  property="user_tasks",
     *                  @OA\Property(property="done", type="number"),
     *                  @OA\Property(property="overdue", type="number"),
     *                  @OA\Property(property="rejected", type="number"),
     *                  @OA\Property(property="returned", type="number"),
     *                  @OA\Property(property="in_progress", type="number"),
     *                  @OA\Property(property="waiting_for_review", type="number"),
     *              ),
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
    public function index()
    {
        $user = auth()->user();

        $level = $user->level;
        $user_id = auth()->id();

        $userProjectIds = $user->projectMembers()->pluck('project_id')->toArray();
        $userActivityIds = $user->activities()->active()->pluck('activity_id')->toArray();

        $projectIdsPlaceholders = implode(',', array_fill(0, count($userProjectIds), '?'));
        $activityIdPlaceholders = implode(',', array_fill(0, count($userActivityIds), '?'));

        $sumTasksQuery = "
            select
                SUM(IF(date(`ended_at`) > SUBDATE(CURDATE(), 1), 1, 0)) as total,
                SUM(IF(date(`started_at`) > CURDATE(), 1, 0)) as upcoming,
                SUM(IF(date(`started_at`) <= CURDATE() and date(`ended_at`) > SUBDATE(CURDATE(), 1), 1, 0)) as available
            from tasks where ((`activity_id` is null and ((`min_level` is null and `max_level` is null) or (`min_level` <= ? and `max_level` >= ?))) or exists (select * from `user_task_assignments` where `tasks`.`id` = `user_task_assignments`.`task_id` and `user_id` = ?)) and not exists (select * from `user_tasks` where `tasks`.`id` = `user_tasks`.`task_id` and `user_id` = ? and `status` != ?)
        ";

        $sumTasksQueryParams = [$level, $level, $user_id, $user_id, 'overdue_by_leave', true];

        if ($activityIdPlaceholders !== '') {
            $sumTasksQuery = "
                select
                    SUM(IF(date(`ended_at`) > SUBDATE(CURDATE(), 1), 1, 0)) as total,
                    SUM(IF(date(`started_at`) > CURDATE(), 1, 0)) as upcoming,
                    SUM(IF(date(`started_at`) <= CURDATE() and date(`ended_at`) > SUBDATE(CURDATE(), 1), 1, 0)) as available
                    from tasks where (((`activity_id` is null or `activity_id` in ($activityIdPlaceholders)) and ((`min_level` is null and `max_level` is null) or (`min_level` <= ? and `max_level` >= ?))) or exists (select * from `user_task_assignments` where `tasks`.`id` = `user_task_assignments`.`task_id` and `user_id` = ?)) and not exists (select * from `user_tasks` where `tasks`.`id` = `user_tasks`.`task_id` and `user_id` = ? and `status` != ?)
            ";

            $sumTasksQueryParams = array_merge($userActivityIds, [$level, $level, $user_id, $user_id, 'overdue_by_leave', true]);
        }

        $sumTasksQuery .= ' and (tasks.number_of_participants = 0 OR tasks.number_of_participants > (select count(*) from user_tasks where user_tasks.task_id = tasks.id)) and (exists (select 1 from `projects` where `tasks`.`project_id` = `projects`.`id` and (`projects`.`public` = ?';

        if ($projectIdsPlaceholders !== '') {
            $sumTasksQuery .= " or `projects`.`id` in ($projectIdsPlaceholders)";
            $sumTasksQueryParams = array_merge($sumTasksQueryParams, $userProjectIds);
        }

        $sumTasksQuery .= ')))';

        $sumTasks = Db::selectOne($sumTasksQuery, $sumTasksQueryParams);
        $sumUserTasks = DB::selectOne("
            select
                SUM(IF(status != 'overdue_by_leave', 1, 0)) as total,
                SUM(IF(status = 'done', 1, 0)) as done,
                SUM(IF(status = 'overdue', 1, 0)) as overdue,
                SUM(IF(status = 'rejected', 1, 0)) as rejected,
                SUM(IF(status = 'returned', 1, 0)) as returned,
                SUM(IF(status = 'in_progress', 1, 0)) as in_progress,
                SUM(IF(status = 'waiting_for_review', 1, 0)) as waiting_for_review
            from user_tasks where user_id = ?
        ", [$user_id]);

        $result = [
            'tasks' => [
                'total' => (int) ($sumTasks->total ?? 0),
                'upcoming' => (int) ($sumTasks->upcoming ?? 0),
                'available' => (int) ($sumTasks->available ?? 0),
            ],
            'user_tasks' => [
                'total' => (int) ($sumUserTasks->total ?? 0),
                'done' => (int) ($sumUserTasks->done ?? 0),
                'overdue' => (int) ($sumUserTasks->overdue ?? 0),
                'rejected' => (int) ($sumUserTasks->rejected ?? 0),
                'returned' => (int) ($sumUserTasks->returned ?? 0),
                'in_progress' => (int) ($sumUserTasks->in_progress ?? 0),
                'waiting_for_review' => (int) ($sumUserTasks->waiting_for_review ?? 0),
            ]
        ];

        return response()->json($result);
    }

    /**
     * Dashboard Overview
     * @OA\Get (
     *     path="/api/dashboard/overview",
     *     tags={"Dashboard"},
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *              @OA\Property(
     *                  property="earned_usdt",
     *                  type="number",
     *                  example="5.8",
     *              ),
     *              @OA\Property(
     *                  property="joined_projects",
     *                  type="number",
     *                  example="5",
     *              ),
     *              @OA\Property(
     *                  property="level_data",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="level",
     *                          type="number",
     *                          example="1",
     *                      ),
     *                      @OA\Property(
     *                          property="points",
     *                          type="number",
     *                          example="90",
     *                      ),
     *                     @OA\Property(
     *                          property="next_level_points",
     *                          type="number",
     *                          example="100",
     *                      ),
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="tasks",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="overdue",
     *                          type="number",
     *                          example="2",
     *                      ),
     *                      @OA\Property(
     *                          property="in_progress",
     *                          type="number",
     *                          example="2",
     *                      ),
     *                     @OA\Property(
     *                          property="on_revision",
     *                          type="number",
     *                          example="2",
     *                      ),
     *                      @OA\Property(
     *                          property="completed",
     *                          type="number",
     *                          example="2",
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

    public function overview()
    {
        $user = auth()->user();
        $projects_count = $user->projectMembers()->count();
        $ambassadorData = DB::selectOne("
            select
                SUM(IF(status != 'overdue_by_leave', 1, 0)) as total,
                SUM(IF(status = 'done', 1, 0)) as done,
                SUM(IF(status = 'overdue', 1, 0)) as overdue,
                SUM(IF(status = 'on_revision', 1, 0)) as on_revision,
                SUM(IF(status = 'in_progress', 1, 0)) as in_progress
            from user_tasks
            where user_id = ?
        ", [$user->id]);
        $position = getUserPositionByLevel(auth()->user()->id, auth()->user()->level);
        $nextLevelPoints = !is_null($position) && isset(config('levels.need_points')[auth()->user()->level])
        && $position <= config('app.minimum_leaderboard_place_level_up')
            ? config('levels.need_points')[auth()->user()->level] : null;
        $ambassadorLevelData = [
            'level' => auth()->user()->level,
            'points' => auth()->user()->points,
            'next_level_points' => $nextLevelPoints,
        ];

        return response()->json([
            'earned_usdt' => 0,
            'joined_projects' => $projects_count,
            'level_data' => $ambassadorLevelData,
            'tasks' => [
                'overdue' => $ambassadorData->overdue,
                'in_progress' => $ambassadorData->in_progress,
                'on_revision' => $ambassadorData->on_revision,
                'completed' => $ambassadorData->done,
            ],
        ]);
    }
}
