<?php

namespace App\Http\Controllers;

use App\Models\{
    Manager,
    Activity,
};

use App\Events\UserUpdated;
use App\Http\Requests\ProfileUpdateRequest;
use App\Notifications\UserApproveActivityNotification;

use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Profile Delete
     * @OA\Delete (
     *     path="/api/profile/delete",
     *     tags={"Profile"},
     *     @OA\Response(
     *         response=204,
     *         description="no content",
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
    public function delete()
    {
        $user = auth()->user();
        $user->delete();

        return response()->noContent();
    }

    /**
     * Profile Update
     * @OA\Put (
     *     path="/api/profile/update",
     *     tags={"Profile"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="password_confirmation",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="country_id",
     *                      type="number",
     *                  ),
     *                  @OA\Property(
     *                      property="skills",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(
     *                              property="id",
     *                              type="number",
     *                          ),
     *                          @OA\Property(
     *                              property="skill_id",
     *                              type="number",
     *                          ),
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                      property="languages",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(
     *                              property="id",
     *                              type="number",
     *                          ),
     *                          @OA\Property(
     *                              property="language_id",
     *                              type="number",
     *                          ),
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                      property="activities",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(
     *                              property="id",
     *                              type="number",
     *                          ),
     *                          @OA\Property(
     *                              property="activity_id",
     *                              type="number",
     *                          ),
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                      property="activity_links",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(
     *                              property="id",
     *                              type="number",
     *                          ),
     *                          @OA\Property(
     *                              property="content",
     *                              type="string",
     *                          ),
     *                          @OA\Property(
     *                              property="activity_link_id",
     *                              type="number",
     *                          ),
     *                      ),
     *                  ),
     *             ),
     *         ),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="no content",
     *      ),
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
    public function update(ProfileUpdateRequest $request)
    {
        $data = $request->validated();
        $user = auth()->user();

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->get('password'));
        }

        if ($request->has('avatar')) {
            $user->clearMediaCollection();

            if ($request->filled('avatar')) {
                $user->addMediaFromRequest('avatar')->toMediaCollection();
            }
        }

        if ($request->filled('country_id')) {
            $user->country()->updateOrCreate([
                'user_id' => $user->id,
            ], [
                'user_id' => $user->id,
                'country_id' => $request->get('country_id'),
            ]);
        }

        // FIXME: duplicate code :c

        $skills = $request->get('skills', []);
        $skillIds = [];

        $activities = $request->get('activities', []);
        $activityIds = [];

        $languages = $request->get('languages', []);
        $languageIds = [];

        $activityLinks = $request->get('activity_links', []);
        $activityLinkIds = [];

        foreach ($activities as $activity) {
            if (isset($activity['id'])) {
                $activityIds[] = $activity['id'];
            }
        }

        $userActivities = $user->activities();
        $userActivities->whereNotIn('id', $activityIds)->delete();

        $upsertActivities = array_map(function ($activity) use ($user) {
            if (!isset($activity['id'])) {
                $activity['id'] = null;
            }

            $activity['user_id'] = $user->id;
            return $activity;
        }, $activities);

        $userActivities->upsert($upsertActivities, ['id']);

        foreach ($skills as $skill) {
            if (isset($skill['id'])) {
                $skillIds[] = $skill['id'];
            }
        }

        $userSkills = $user->skills();
        $userSkills->whereNotIn('id', $skillIds)->delete();

        $upsertSkills = array_map(function ($skill) use ($user) {
            if (!isset($skill['id'])) {
                $skill['id'] = null;
            }

            $skill['user_id'] = $user->id;
            return $skill;
        }, $skills);

        $userSkills->upsert($upsertSkills, ['id']);

        foreach ($activities as $activity) {
            if (isset($activity['id'])) {
                $activityIds[] = $activity['id'];
            }
        }

        $userActivities = $user->activities();
        $userActivities->whereNotIn('id', $activityIds)->delete();

        $upsertActivities = array_map(function ($activity) use ($user) {
            if (!isset($activity['id'])) {
                $activity['id'] = null;
            }

            $activity['user_id'] = $user->id;
            return $activity;
        }, $activities);

        $userActivities->upsert($upsertActivities, ['id']);

        foreach ($languages as $language) {
            if (isset($language['id'])) {
                $languageIds[] = $language['id'];
            }
        }

        $userLanguages = $user->languages();
        $userLanguages->whereNotIn('id', $languageIds)->delete();

        $upsertLanguages = array_map(function ($language) use ($user) {
            if (!isset($language['id'])) {
                $language['id'] = null;
            }

            $language['user_id'] = $user->id;
            return $language;
        }, $languages);

        $userLanguages->upsert($upsertLanguages, ['id', 'content']);

        foreach ($activityLinks as $activityLink) {
            if (isset($activityLink['id'])) {
                $activityLinkIds[] = $activityLink['id'];
            }
        }

        $userActivityLinks = $user->activityLinks();
        $userActivityLinks->whereNotIn('id', $activityLinkIds)->delete();

        $upsertActivityLinks = array_map(function ($activityLink) use ($user) {
            if (!isset($activityLink['id'])) {
                $activityLink['id'] = null;
            }

            $activityLink['user_id'] = $user->id;
            return $activityLink;
        }, $activityLinks);

        $userActivityLinks->upsert($upsertActivityLinks, ['id', 'content']);

        $user->update($data);
        UserUpdated::dispatch($user);

        dispatch(static function () use ($user, $upsertActivities) {
            $activityIds = [];
            array_walk($upsertActivities, static function ($activity) use (&$activityIds) {
                if (is_null($activity['id'])) {
                    $activityIds[] = $activity['activity_id'];
                }
            });

            $activityNames = Activity::whereIn('id', $activityIds)->pluck('name')->join(', ');
            if ($activityNames !== '') {
                $managers = Manager::whereHas('allRoles', static function ($query) {
                    $query->whereIn('name', ['Super Admin', 'Catapult Manager']);
                })->get();

                $managers->each->notify(new UserApproveActivityNotification($activityNames, $user));
            }
        })->afterResponse();

        return response()->noContent();
    }
}
