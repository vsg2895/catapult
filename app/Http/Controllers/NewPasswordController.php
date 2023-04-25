<?php

namespace App\Http\Controllers;

use App\Http\Requests\{
    NewPasswordResetRequest,
    NewPasswordRefreshRequest,
};

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;

class NewPasswordController extends Controller
{
    /**
     * Sent reset link for refresh password
     * @OA\Post (
     *     path="/api/auth/refresh-password",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                  ),
     *             ),
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="status"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="errors",
     *      ),
     * )
     */
    public function refresh(NewPasswordRefreshRequest $request)
    {
        $status = Password::sendResetLink($request->only('email'));
        if ($status === Password::RESET_LINK_SENT) {
            return [
                'status' => __($status),
            ];
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    /**
     * Reset password by token
     * @OA\Post (
     *     path="/api/auth/reset-password/{token}",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *         in="path",
     *         name="token",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="email",
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
     *             ),
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Password reset successfuly"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="invalid",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="errors",
     *      ),
     * )
     */
    public function reset($token, NewPasswordResetRequest $request)
    {
        $status = Password::reset(
            array_merge($request->only('email', 'password', 'password_confirmation'), ['token' => $token]),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->get('password')),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            return response([
                'message' => 'Password reset successfully',
            ]);
        }

        return response([
            'message' => __($status),
        ], 400);
    }
}
