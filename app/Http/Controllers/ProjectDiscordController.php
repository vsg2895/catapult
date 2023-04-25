<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Contracts\DiscordServiceContract;

class ProjectDiscordController extends Controller
{
    private DiscordServiceContract $discordService;

    public function __construct(DiscordServiceContract $discordService)
    {
        $this->discordService = $discordService;
    }

    /**
     * Get project discord guild
     * @OA\Get (
     *     path="/api/projects/{project}/discord/guild",
     *     @OA\Parameter(
     *         in="path",
     *         name="project",
     *         required=true,
     *         @OA\Schema(type="number"),
     *     ),
     *     tags={"Projects Discord Guild"},
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="string"),
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(
     *                  property="roles",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="string"),
     *                      @OA\Property(property="name", type="string"),
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="channels",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="string"),
     *                      @OA\Property(property="name", type="string"),
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="newTask",
     *                  nullable=true,
     *                  @OA\Property(property="active", type="boolean"),
     *                  @OA\Property(property="channelId", type="string"),
     *              ),
     *              @OA\Property(
     *                  property="newProject",
     *                  nullable=true,
     *                  @OA\Property(property="active", type="boolean"),
     *                  @OA\Property(property="channelId", type="string"),
     *              ),
     *              @OA\Property(
     *                  property="completedTask",
     *                  nullable=true,
     *                  @OA\Property(property="active", type="boolean"),
     *                  @OA\Property(property="channelId", type="string"),
     *              ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="invalid",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Discord bot is not connected to this project!",
     *             ),
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
    public function index(Project $project)
    {
        // FIXME: Duplicate check discord provider
        $socialProvider = $project->discordProvider();
        if (!$socialProvider) {
            return response()->json([
                'message' => 'Discord bot is not connected to this project!',
            ], 400);
        }

        $guild = $this->discordService->getGuild($socialProvider->provider_id);
        return response()->json($guild + ($socialProvider->notifications ?? []));
    }

    /**
     * Get project discord guild roles
     * @OA\Get (
     *     path="/api/projects/{project}/discord/guild/roles",
     *     @OA\Parameter(
     *         in="path",
     *         name="project",
     *         required=true,
     *         @OA\Schema(type="number"),
     *     ),
     *     tags={"Projects Discord Guild"},
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="string"),
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="editable", type="boolean"),
     *              @OA\Property(property="position", type="number"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="invalid",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Discord bot is not connected to this project!",
     *             ),
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
    public function roles(Project $project)
    {
        // FIXME: Duplicate check discord provider
        $socialProvider = $project->discordProvider();
        if (!$socialProvider) {
            return response()->json([
                'message' => 'Discord bot is not connected to this project!',
            ], 400);
        }

        return response()->json($this->discordService->getGuildRoles($socialProvider->provider_id));
    }
}
