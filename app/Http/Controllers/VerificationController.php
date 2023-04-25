<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationController extends Controller
{
    /**
     * Verify email
     * @OA\Get (
     *     path="/api/auth/email/verify/{id}/{hash}",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="number"),
     *     ),
     *     @OA\Parameter(
     *         in="path",
     *         name="hash",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="errors",
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="no content",
     *     ),
     *     security={{ "apiAuth": {} }},
     * )
     */
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return response()->noContent();
    }

    /**
     * Resend verification notification
     * @OA\Post (
     *     path="/api/auth/email/verification-notification",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=204,
     *         description="no content",
     *     ),
     *     security={{ "apiAuth": {} }},
     * )
     */
    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return response()->noContent();
    }
}
