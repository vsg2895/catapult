<?php

namespace App\Http\Controllers;

use App\Events\UserUpdated;
use App\Models\SocialProvider;
use App\Models\User;

use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\{DB, Log};
use Laravel\Socialite\Two\AbstractProvider;

class SocialAuthenticationController extends Controller
{
    /**
     * Redirect url for user social authentication
     * @OA\Get (
     *     path="/api/auth/{provider}/redirect",
     *     tags={"Social Authentication"},
     *     @OA\Parameter(
     *         in="path",
     *         name="provider",
     *         required=true,
     *         @OA\Schema(
     *              type="string",
     *              enum={"discord", "twitter", "telegram"},
     *              example={"discord", "twitter", "telegram"},
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *              @OA\Property(property="redirect_url", type="string"),
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
    public function redirectProvider(string $provider)
    {
        return [
            'redirect_url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl(),
        ];
    }

    /**
     * Callback for user social authentication
     * @OA\Get (
     *     path="/api/auth/{provider}/callback",
     *     tags={"Social Authentication"},
     *     @OA\Parameter(
     *         in="path",
     *         name="provider",
     *         required=true,
     *         @OA\Schema(
     *              type="string",
     *              enum={"discord", "twitter", "telegram"},
     *              example={"discord", "twitter", "telegram"},
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="token_type"),
     *             @OA\Property(property="expires_in", type="number", example="60"),
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
     *             @OA\Property(property="error", type="string", example="Social provider already attached!"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized"),
     *         ),
     *     ),
     * )
     */
    public function handleProviderCallback(string $provider)
    {
        try {
            /**
             * @var AbstractProvider $socialite
             */
            $socialite = Socialite::driver($provider);
            $socialiteUser = $socialite->stateless()->user();

            if ($provider !== 'telegram' && auth()->check()) {
                $socialProviderExists = SocialProvider::where('provider_id', $socialiteUser->getId())
                    ->where('provider_name', $provider)
                    ->whereRaw('NOT (model_id = ? and model_type = ?)', [auth()->id(), 'App\Models\User'])
                    ->exists();

                if ($socialProviderExists) {
                    return response()->json([
                        'message' => 'Social provider already attached!',
                    ], 400);
                }
            }

            $socialiteProvider = SocialProvider::where('provider_id', $socialiteUser->getId())
                ->where('provider_name', $provider)
                ->where('model_type', 'App\Models\User')
                ->first();

            DB::beginTransaction();

            $user = auth()->user() ?? (
                $socialiteProvider->model ?? User::firstOrCreate(['email' => $socialiteUser->getEmail()])
            );

            $user->socialProviders()->firstOrCreate([
                'provider_id' => $socialiteUser->getId(),
                'provider_name' => $provider,
            ], [
                'name' => $socialiteUser->getNickname() ?: $socialiteUser->getName(),
            ]);

            DB::commit();
            UserUpdated::dispatch($user);

            return response()->json($this->respondWithToken(auth()->login($user)));
        } catch (Exception $error) {
            DB::rollBack();
            Log::error($error);

            return response()->json([
                'message' => 'Oops, account connection failed!',
            ], 400);
        }
    }

    /**
     * Delete user social authentication
     * @OA\Delete (
     *     path="/api/auth/{provider}",
     *     tags={"Social Authentication"},
     *     @OA\Parameter(
     *         in="path",
     *         name="provider",
     *         required=true,
     *         @OA\Schema(
     *              type="string",
     *              enum={"discord", "twitter", "telegram"},
     *              example={"discord", "twitter", "telegram"},
     *         ),
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
     *         response=401,
     *         description="unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *     security={{ "apiAuth": {} }},
     * )
     */
    public function destroy(string $provider)
    {
        $user = auth()->user();
        $user->socialProviders()->where('provider_name', $provider)->delete();
        return response()->noContent();
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return array
     */
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL()
        ];
    }
}
