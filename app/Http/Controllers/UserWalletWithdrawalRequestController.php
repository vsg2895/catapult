<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserWalletWithdrawalRequest;
use App\Http\Resources\UserWalletWithdrawalRequestCollection;

use Brick\Math\BigDecimal;
use Illuminate\Validation\ValidationException;

class UserWalletWithdrawalRequestController extends Controller
{
    /**
     * Get List User Withdrawal Requests
     * @OA\Get (
     *     path="/api/user-wallets/withdrawal-requests",
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
     *                  property="status",
     *                  type="string",
     *                  enum={"pending", "canceled", "executed", "accepted"},
     *                  example="pending",
     *              ),
     *              @OA\Property(
     *                  property="tx_hash",
     *                  type="string",
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
    public function index()
    {
        $userWithdrawalRequests = auth()->user()->withdrawalRequests()->with(['wallet'])->paginate(10);
        return response()->json(new UserWalletWithdrawalRequestCollection($userWithdrawalRequests));
    }

    /**
     * Create user wallet withdrawal request
     * @OA\Post (
     *     path="/api/user-wallets/withdrawal-requests",
     *     tags={"User Wallets"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="value",
     *                      type="number",
     *                  ),
     *                  @OA\Property(
     *                      property="user_wallet_id",
     *                      type="number",
     *                  ),
     *             ),
     *         ),
     *      ),
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
     *                  property="status",
     *                  type="string",
     *                  enum={"pending", "canceled", "executed", "accepted"},
     *                  example="pending",
     *              ),
     *              @OA\Property(
     *                  property="tx_hash",
     *                  type="string",
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
    public function store(UserWalletWithdrawalRequest $request)
    {
        $user = auth()->user();
        $value = $request->get('value');

        $userWallet = $user->wallets()->where('id', $request->get('user_wallet_id'))->first();

        if (optional($userWallet)->address === '') {
            throw ValidationException::withMessages(['user_wallet_id' => 'Wallet address can not be empty!']);
        }

        if (BigDecimal::of($value)->isGreaterThan($userWallet->balance)) {
            throw ValidationException::withMessages(['value' => 'Insufficient balance for withdraw!']);
        }

        $userWallet->balance = (string) BigDecimal::of($userWallet->balance)->minus($value);
        $userWallet->save();

        $userWithdrawalRequest = $user->withdrawalRequests()->create(array_merge($request->validated(), ['tx_hash' => '-']));
        return response()->json($userWithdrawalRequest);
    }
}
