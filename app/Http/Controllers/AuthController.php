<?php

namespace App\Http\Controllers;

use App\Models\{User, UserWallet};

use App\Http\Requests\{
    LoginRequest,
    RegisterRequest,
    SignatureRequest,
    VerifySignatureRequest,
};

use App\Services\SignatureService;
use App\Http\Resources\User as UserResource;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @var SignatureService
     */
    private SignatureService $signatureService;

    public function __construct(SignatureService $signatureService)
    {
        $this->signatureService = $signatureService;
    }

    /**
     * Login user
     * @OA\Post (
     *     path="/api/auth/login",
     *     tags={"Auth"},
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
     *          response=204,
     *          description="no content",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="errors",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string"),
     *          ),
     *      ),
     * )
     */
    public function login(LoginRequest $request)
    {
        if (! $token = JWTAuth::attempt($request->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Register user
     * @OA\Post (
     *     path="/api/auth/register",
     *     tags={"Auth"},
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
     *          response=204,
     *          description="no content",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="errors",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string"),
     *          ),
     *      ),
     * )
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        event(new Registered($user));
        return response()->noContent();
    }

    /**
     * Get the authenticated User.
     * @OA\Get (
     *     path="/api/auth/me",
     *     tags={"Auth"},
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="id",
     *                  type="number",
     *                  example="1",
     *              ),
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  example="example name",
     *              ),
     *              @OA\Property(
     *                  property="phone",
     *                  type="string",
     *                  example="example phone",
     *              ),
     *              @OA\Property(
     *                  property="email",
     *                  type="string",
     *                  example="test@test.com",
     *              ),
     *              @OA\Property(
     *                  property="level",
     *                  type="number",
     *                  example="1",
     *              ),
     *              @OA\Property(
     *                  property="avatar",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="wallet",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="next_level",
     *                  type="boolean",
     *                  example="false",
     *              ),
     *              @OA\Property(
     *                  property="need_points",
     *                  type="number",
     *                  example="0",
     *              ),
     *              @OA\Property(
     *                  property="total_points",
     *                  type="number",
     *                  example="0",
     *              ),
     *              @OA\Property(
     *                  property="total_balance",
     *                  type="number",
     *                  example="0",
     *              ),
     *              @OA\Property(
     *                  property="set_up_profile",
     *                  @OA\Property(property="name", type="boolean", example="false"),
     *                  @OA\Property(property="languages_count", type="boolean", example="false"),
     *                  @OA\Property(property="country", type="boolean", example="false"),
     *                  @OA\Property(property="email", type="boolean", example="false"),
     *                  @OA\Property(property="discord_social_provider", type="boolean", example="false"),
     *                  @OA\Property(property="telegram_social_provider", type="boolean", example="false"),
     *                  @OA\Property(property="twitter_social_provider", type="boolean", example="false"),
     *                  @OA\Property(property="wallet", type="boolean", example="false"),
     *                  @OA\Property(property="activities_count", type="boolean", example="false"),
     *                  @OA\Property(property="skills_count", type="boolean", example="false"),
     *                  @OA\Property(property="percentage", type="number", example="10"),
     *              ),
     *              @OA\Property(
     *                  property="has_task_limit",
     *                  type="boolean",
     *                  example="false",
     *              ),
     *              @OA\Property(
     *                  property="completed_profile_reward",
     *                  type="boolean",
     *                  example="false",
     *              ),
     *              @OA\Property(
     *                  property="skills",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="id",
     *                          type="number",
     *                      ),
     *                      @OA\Property(
     *                          property="skill",
     *                          @OA\Property(
     *                              property="id",
     *                              type="number",
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                          ),
     *                      ),
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="country",
     *                  type="string",
     *                  nullable="true",
     *                  example="Ukraine",
     *              ),
     *              @OA\Property(
     *                  property="languages",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="id",
     *                          type="number",
     *                      ),
     *                      @OA\Property(
     *                          property="language",
     *                          @OA\Property(
     *                              property="id",
     *                              type="number",
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                          ),
     *                      ),
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="activities",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="id",
     *                          type="number",
     *                      ),
     *                      @OA\Property(
     *                          property="activity",
     *                          @OA\Property(
     *                              property="id",
     *                              type="number",
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                          ),
     *                      ),
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="activity_links",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="id",
     *                          type="number",
     *                      ),
     *                      @OA\Property(
     *                          property="content",
     *                          type="string",
     *                      ),
     *                      @OA\Property(
     *                          property="activity_link",
     *                          @OA\Property(
     *                              property="id",
     *                              type="number",
     *                          ),
     *                          @OA\Property(
     *                              property="link_id",
     *                              type="number",
     *                          ),
     *                          @OA\Property(
     *                              property="activity_id",
     *                              type="number",
     *                          ),
     *                          @OA\Property(
     *                              property="link",
     *                              @OA\Property(
     *                                  property="name",
     *                                  type="string",
     *                              ),
     *                              @OA\Property(
     *                                  property="icon",
     *                                  type="string",
     *                                  nullable="true",
     *                              ),
     *                          ),
     *                      ),
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="social_links",
     *                  type="array",
     *                  deprecated=true,
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="id",
     *                          type="number",
     *                      ),
     *                      @OA\Property(
     *                          property="content",
     *                          type="string",
     *                      ),
     *                      @OA\Property(
     *                          property="social_link",
     *                          @OA\Property(
     *                              property="id",
     *                              type="number",
     *                          ),
     *                          @OA\Property(
     *                              property="name",
     *                              type="string",
     *                          ),
     *                      ),
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="social_providers",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="id",
     *                          type="number",
     *                          example="1",
     *                      ),
     *                      @OA\Property(
     *                          property="name",
     *                          type="string",
     *                          example="test#12345",
     *                      ),
     *                      @OA\Property(
     *                          property="provider_id",
     *                          type="number",
     *                          example="1",
     *                      ),
     *                      @OA\Property(
     *                          property="provider_name",
     *                          type="string",
     *                          example="discord",
     *                      ),
     *                  ),
     *              ),
     *          ),
     *      ),
     *     @OA\Response(
     *         response=401,
     *         description="unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated."),
     *         ),
     *     ),
     *     security={{ "apiAuth": {} }},
     * )
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth()->user();

        $user->loadCount(['tasksInWork']);
        $user->load([
            'media',
            'tasks',
            'tasks.task',
            'skills',
            'skills.skill',
            'country',
            'country.country',
            'languages',
            'languages.language',
            'activities',
            'activities.activity',
            'activityLinks',
            'activityLinks.link',
            'activityLinks.link.link',
            'activityLinks.link.link.media',
            'socialLinks',
            'socialLinks.link',
            'socialLinks.link.media',
            'socialProviders',
        ]);

        $user->position = getUserPositionByLevel($user->id, $user->level);
        return response()->json(new UserResource($user));
    }

    /**
     * Log the user out (Invalidate the token).
     * @OA\Post (
     *     path="/api/auth/logout",
     *     tags={"Auth"},
     *     @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Successfully logged out"),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="unauthorized",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *         ),
     *     ),
     *     security={{ "apiAuth": {} }},
     * )
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     * @OA\Post (
     *     path="/api/auth/refresh",
     *     tags={"Auth"},
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
     *         response=401,
     *         description="unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated."),
     *         ),
     *     ),
     * )
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return response()->json($this->respondWithToken(JWTAuth::refresh(JWTAuth::getToken())));
    }

    /**
     * Get signature
     * @OA\Post (
     *     path="/api/auth/signature",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="address",
     *                      type="string",
     *                  ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="nonce", type="string"),
     *              @OA\Property(property="message", type="string"),
     *          ),
     *     ),
     * )
     */
    public function signature(SignatureRequest $request)
    {
        $nonce = Str::random();
        $address = $request->get('address');

        if (UserWallet::where('address', $address)->exists() && auth()->check()) {
            return response()->json([
                'message' => 'Wallet already attached!',
            ], 400);
        }

        $user = auth()->user() ?? User::firstOrCreate(['wallet' => $address]);
        $user->update(['nonce' => $nonce]);

        return response()->json([
            'nonce' => $nonce,
            'message' => $this->signatureService->generate($nonce),
        ]);
    }

    /**
     * Authenticate by wallet
     * @OA\Post (
     *     path="/api/auth/verify-signature",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="address",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="signature",
     *                      type="string",
     *                  ),
     *             ),
     *         ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="invalid",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="access_token", type="string"),
     *              @OA\Property(property="token_type", type="string", example="token_type"),
     *              @OA\Property(property="expires_in", type="number", example="60"),
     *          ),
     *      ),
     * )
     */
    public function verifySignature(VerifySignatureRequest $request)
    {
        $address = $request->get('address');

        $user = auth()->user() ?? User::firstWhere('wallet', $address);
        if (!optional($user)->nonce || !$this->signatureService->verify($user->nonce, $request->get('signature'), $address)) {
            throw ValidationException::withMessages(['signature' => 'Signature verification failed']);
        }

        $user->loadCount('wallets');
        $user->wallets()->firstOrCreate([
            'address' => $address,
        ], [
            'balance' => 0,
            'is_primary' => $user->wallets_count === 0,
            'coin_type_id' => 1,
        ]);

        $user->update(['nonce' => null]);
        return response()->json($this->respondWithToken(auth()->login($user)));
    }

    /**
     * Validate name
     * @OA\Post (
     *     path="/api/auth/validate-name",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                  ),
     *             ),
     *         ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="invalid",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="no content",
     *      ),
     * )
     */
    public function validateName(Request $request)
    {
        $request->validate([
            'name' => 'required|alpha_num|min:3|max:29|unique:users,name',
        ]);

        return response()->noContent();
    }

    /**
     * Validate email
     * @OA\Post (
     *     path="/api/auth/validate-email",
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
     *          response=422,
     *          description="invalid",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="no content",
     *      ),
     * )
     */
    public function validateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
        ]);

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
