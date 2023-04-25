<?php

namespace App\Http\Controllers;

use Brick\Math\BigDecimal;
use App\Http\Requests\{
    UserWalletCreateRequest,
    UserWalletUpdateRequest,
};

use App\Http\Resources\{
    UserWallet as UserWalletResource,
    UserWalletHistoryCollection,
};

use App\Models\UserWallet;

use Illuminate\Validation\ValidationException;

class UserWalletController extends Controller
{
    /**
     * Get List User Wallets
     * @OA\Get (
     *     path="/api/user-wallets",
     *     tags={"User Wallets"},
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
     *                  property="balance",
     *                  type="number",
     *                  example="1",
     *              ),
     *              @OA\Property(
     *                  property="address",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="coin_type",
     *                  nullable=true,
     *                  @OA\Property(property="id", type="number", example="1"),
     *                  @OA\Property(property="name", type="string", example="example name"),
     *                  @OA\Property(property="type_of_chain", type="string", example="example name"),
     *              ),
     *              @OA\Property(
     *                  property="is_primary",
     *                  type="boolean",
     *                  example="true",
     *              ),
     *              @OA\Property(
     *                  property="balance_in_usd",
     *                  type="number",
     *                  example="1",
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
    public function index()
    {
        $userWallets = auth()->user()->wallets;
        return response()->json(UserWalletResource::collection($userWallets));
    }

    /**
     * Create user wallet
     * @OA\Post (
     *     path="/api/user-wallets",
     *     tags={"User Wallets"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="address",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="coin_type_id",
     *                      type="number",
     *                  ),
     *             ),
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example="1"),
     *              @OA\Property(property="address", type="string", example="example name"),
     *              @OA\Property(property="balance", type="number", example="1"),
     *              @OA\Property(
     *                  property="coin_type",
     *                  nullable=true,
     *                  @OA\Property(property="id", type="number", example="1"),
     *                  @OA\Property(property="name", type="string", example="example name"),
     *                  @OA\Property(property="type_of_chain", type="string", example="example name"),
     *              ),
     *              @OA\Property(property="is_primary", type="boolean", example="true"),
     *              @OA\Property(property="balance_in_usd", type="number", example="1"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="errors",
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
    public function store(UserWalletCreateRequest $request)
    {
        $userWallets = auth()->user()->wallets();
        if ($userWallets->where('address', $request->get('address'))->where('coin_type_id', $request->get('coin_type_id'))->exists()) {
            throw ValidationException::withMessages(['address' => 'This wallet already exists!']);
        }

        $userWallet = auth()->user()->wallets()->create(array_merge($request->validated(), ['balance' => 0]));
        return response()->json($userWallet);
    }

    /**
     * Update user wallet
     * @OA\Put (
     *     path="/api/user-wallets/{userWallet}",
     *     @OA\Parameter(
     *         in="path",
     *         name="userWallet",
     *         required=true,
     *         @OA\Schema(type="number"),
     *     ),
     *     tags={"User Wallets"},
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
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example="1"),
     *              @OA\Property(property="address", type="string", example="example name"),
     *              @OA\Property(property="balance", type="number", example="1"),
     *              @OA\Property(
     *                  property="coin_type",
     *                  nullable=true,
     *                  @OA\Property(property="id", type="number", example="1"),
     *                  @OA\Property(property="name", type="string", example="example name"),
     *                  @OA\Property(property="type_of_chain", type="string", example="example name"),
     *              ),
     *              @OA\Property(property="is_primary", type="boolean", example="true"),
     *              @OA\Property(property="balance_in_usd", type="number", example="1"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="errors",
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
    public function update(UserWallet $userWallet, UserWalletUpdateRequest $request)
    {
        $userWallet->update($request->validated());
        return response()->json($userWallet);
    }

    /**
     * Delete user wallet
     * @OA\Delete (
     *     path="/api/user-wallets/{userWallet}",
     *     tags={"User Wallets"},
     *     @OA\Parameter(
     *         in="path",
     *         name="userWallet",
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
     *         response=401,
     *         description="unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *     security={{ "apiAuth": {} }},
     * )
     */
    public function destroy(UserWallet $userWallet)
    {
        $userWallet->delete();
        return response()->noContent();
    }

    /**
     * Get List User Wallet History
     * @OA\Get (
     *     path="/api/user-wallets/history",
     *     tags={"User Wallets"},
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
     *                  property="date",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="value",
     *                  type="number",
     *                  example="1",
     *              ),
     *              @OA\Property(
     *                  property="points",
     *                  type="number",
     *                  example="1",
     *              ),
     *              @OA\Property(
     *                  property="value_in_usd",
     *                  type="number",
     *                  example="1",
     *              ),
     *              @OA\Property(
     *                  property="user_wallet",
     *                  @OA\Property(
     *                      property="id",
     *                      type="number",
     *                      example="1",
     *                  ),
     *                  @OA\Property(
     *                      property="balance",
     *                      type="number",
     *                      example="1",
     *                  ),
     *                  @OA\Property(
     *                      property="address",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="coin_type",
     *                      nullable=true,
     *                      @OA\Property(property="id", type="number", example="1"),
     *                      @OA\Property(property="name", type="string", example="example name"),
     *                      @OA\Property(property="type_of_chain", type="string", example="example name"),
     *                  ),
     *                  @OA\Property(
     *                      property="is_primary",
     *                      type="boolean",
     *                      example="true",
     *                  ),
     *                  @OA\Property(
     *                      property="balance_in_usd",
     *                      type="number",
     *                      example="1",
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
     *             @OA\Property(property="message", type="string", example="Unauthenticated."),
     *         ),
     *     ),
     *     security={{ "apiAuth": {} }},
     * )
     */
    public function history()
    {
        $userHistoryWallets = auth()->user()->historyWallets()->with(['wallet', 'task', 'task.project'])->paginate(10);
        return response()->json(new UserWalletHistoryCollection($userHistoryWallets));
    }

    /**
     * Get User Wallets Total Sum
     * @OA\Get (
     *     path="/api/user-wallets/total",
     *     tags={"User Wallets"},
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *              @OA\Property(
     *                  property="USD",
     *                  type="number",
     *                  example="592802.972609139",
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
    public function total()
    {
        $sum = [
            'USD' => 0
        ];
        auth()->user()->wallets()->get()->pluck('balance_in_usd')->each(function (BigDecimal $balance) use (&$sum) {
            $sum['USD'] += $balance->toFloat();
        });

        return $sum;
    }
}
