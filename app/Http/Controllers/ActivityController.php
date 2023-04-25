<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Http\Resources\Activity as ActivityResource;

class ActivityController extends Controller
{
    /**
     * Get List Activities
     * @OA\Get (
     *     path="/api/activities",
     *     tags={"Activities"},
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
     *                  property="links",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="number"),
     *                      @OA\Property(
     *                          property="link",
     *                          type="array",
     *                          @OA\Items(
     *                              @OA\Property(property="id", type="number"),
     *                              @OA\Property(property="name", type="string"),
     *                              @OA\Property(property="icon", type="string", nullable="true"),
     *                          ),
     *                      ),
     *                      @OA\Property(property="link_id", type="number"),
     *                      @OA\Property(property="activity_id", type="number"),
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="skills",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="number"),
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="activity_id", type="number"),
     *                  ),
     *              ),
     *         ),
     *     ),
     * )
     */
    public function index()
    {
        $activities = Activity::with([
            'links',
            'links.link',
            'links.link.media',
            'skills',
        ])->get();

        return response()->json(ActivityResource::collection($activities));
    }
}
