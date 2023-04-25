<?php

namespace App\Http\Controllers;

use App\Events\InviteUpdated;
use App\Models\{Invitation, ProjectMember};

class InvitationController extends Controller
{
    /**
     * Verify Invitation
     * @OA\Post (
     *     path="/api/invitations/verify/{token}",
     *     @OA\Parameter(
     *         in="path",
     *         name="token",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     tags={"Invitations"},
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *              @OA\Property(property="project_id", type="number", example="1"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found",
     *     ),
     * )
     */
    public function verify(Invitation $invitation)
    {
        return response()->json(['project_id' => $invitation->project_id]);
    }

    /**
     * Revoke Invitation
     * @OA\Get (
     *     path="/api/invitation/revoke/{token}",
     *     @OA\Parameter(
     *         in="path",
     *         name="token",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     tags={"Invitations"},
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
     *     ),
     * )
     */
    public function revoke(Invitation $invitation)
    {
        if (in_array($invitation->status, [
            Invitation::STATUS_REVOKED,
            Invitation::STATUS_ACCEPTED,
            Invitation::STATUS_DECLINED,
        ], true)) {
            return response()->json([
                'message' => 'Incorrect invitation!',
            ], 400);
        }

        $invitation->update(['status' => Invitation::STATUS_REVOKED]);

        // FIXME: manager_pusher?
        broadcast(new InviteUpdated($invitation->userable_id, $invitation->token, Invitation::STATUS_REVOKED))
            ->via('manager_pusher');

        return response()->noContent();
    }

    /**
     * Accept Invitation
     * @OA\Get (
     *     path="/api/invitation/accept/{token}",
     *     @OA\Parameter(
     *         in="path",
     *         name="token",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     tags={"Invitations"},
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
     *     ),
     * )
     */
    public function accept(Invitation $invitation)
    {
        $user = $invitation->userable;
        if (!$user) {
            return response()->json([
                'message' => 'User need to be created first!',
            ], 400);
        }

        if (in_array($invitation->status, [
            Invitation::STATUS_REVOKED,
            Invitation::STATUS_ACCEPTED,
            Invitation::STATUS_DECLINED,
        ], true)) {
            return response()->json([
                'message' => 'Incorrect invitation!',
            ], 400);
        }

        $projectId = $invitation->project_id;
        $user->projectMembers()->firstOrCreate([
            'project_id' => $projectId,
        ], [
            'status' => ProjectMember::STATUS_ACCEPTED,
            'project_id' => $projectId,
        ]);

        $invitation->update(['status' => Invitation::STATUS_ACCEPTED]);

        // FIXME: manager_pusher?
        broadcast(new InviteUpdated($invitation->userable_id, $invitation->token, Invitation::STATUS_ACCEPTED))
            ->via('manager_pusher');

        return response()->noContent();
    }
}
