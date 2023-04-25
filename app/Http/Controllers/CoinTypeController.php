<?php

namespace App\Http\Controllers;

use App\Models\CoinType;
use App\Http\Resources\CoinType as CoinTypeResource;

class CoinTypeController extends Controller
{
    /**
     * Get List Coin Types
     * @OA\Get (
     *     path="/api/coin-types",
     *     tags={"Coin Types"},
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
     *                  property="rpc_url",
     *                  type="string",
     *                  example="example rpc url"
     *              ),
     *              @OA\Property(
     *                  property="chain_id",
     *                  type="number",
     *                  example="1",
     *              ),
     *              @OA\Property(
     *                  property="type_of_chain",
     *                  type="string",
     *                  example="example type of chain",
     *              ),
     *              @OA\Property(
     *                  property="block_explorer_url",
     *                  type="string",
     *                  example="example block explorer url",
     *              ),
     *         ),
     *     ),
     *     security={{ "apiAuth": {} }},
     * )
     */
    public function index()
    {
        $coinTypes = CoinType::all();
        return response()->json(CoinTypeResource::collection($coinTypes));
    }
}
