<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Resources\Project as ProjectResource;

use Illuminate\Http\Request;

class UserProjectController extends Controller
{
    /**
     * Get list user projects
     * @OA\Get (
     *     path="/api/user-projects",
     *     tags={"User Projects"},
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example="1"),
     *              @OA\Property(
     *                  property="project",
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
     *                      property="logo",
     *                      type="string",
     *                      example="cdn.com/logo.png"
     *                  ),
     *                  @OA\Property(
     *                      property="banner",
     *                      type="string",
     *                      example="cdn.com/banner.png"
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      type="string",
     *                      example="example description"
     *                  ),
     *                  @OA\Property(
     *                      property="pool_amount",
     *                      type="number",
     *                      example="100"
     *                  ),
     *                  @OA\Property(
     *                      property="medium_username",
     *                      type="string",
     *                      example="@username"
     *                  ),
     *                  @OA\Property(
     *                      property="tags",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="number", example="1"),
     *                          @OA\Property(property="content", type="number", example="example.com"),
     *                          @OA\Property(
     *                              property="tag",
     *                              @OA\Property(property="id", type="number", example="1"),
     *                              @OA\Property(property="name", type="string", example="Telegram"),
     *                          ),
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
    public function index()
    {
        $userProjects = auth()->user()->projectMembers()->with(['project', 'project.media'])->get();
        $userProjects = $userProjects->map(fn ($userProject) => $userProject->project);
        return response()->json(ProjectResource::collection($userProjects));
    }

    /**
     * User join to project
     * @OA\Post (
     *     path="/api/user-projects/{project}",
     *     tags={"User Projects"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="referral_code",
     *                      type="string",
     *                  ),
     *             ),
     *         ),
     *     ),
     *     @OA\Parameter(
     *         in="path",
     *         name="project",
     *         required=true,
     *         @OA\Schema(type="number"),
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="no content",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="invalid",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="User already joined!"),
     *         ),
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
    public function store(Project $project, Request $request)
    {
        $request->validate([
            'referral_code' => 'uuid|exists:user_tasks,referral_code',
        ]);

        $user = auth()->user();
        if ($user->projectMembers()->where('project_id', $project->id)->exists()) {
            return response()->json([
                'message' => 'User already joined!',
            ], 400);
        }

        $user->projectMembers()->create([
            'project_id' => $project->id,
            'referral_code' => $request->get('referral_code'),
        ]);

        return response()->noContent();
    }

    /**
     * User leave from project
     * @OA\Delete (
     *     path="/api/user-projects/{project}",
     *     tags={"User Projects"},
     *     @OA\Parameter(
     *         in="path",
     *         name="project",
     *         required=true,
     *         @OA\Schema(type="number"),
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="no content",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="invalid",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="User not member!"),
     *         ),
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
    public function destroy(Project $project)
    {
        $user = auth()->user();
        if (!$user->projectMembers()->where('project_id', $project->id)->exists()) {
            return response()->json([
                'message' => 'User not member!',
            ], 400);
        }

        if ($user->tasksInWork()->whereRelation('task', 'project_id', $project->id)->exists()) {
            return response()->json([
                'message' => 'Complete all project tasks before leave!',
            ], 400);
        }

        $user->projectMembers()->where('project_id', $project->id)->delete();
        return response()->noContent();
    }
}
