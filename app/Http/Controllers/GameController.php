<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Game\BullCowGame;
use App\Repository\GameRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
class GameController extends Controller
{
    private BullCowGame $game;
    public function __construct(GameRepository $game_repository)
    {
        $this->game = new BullCowGame($game_repository);
    }

    /**
     * * @OA\Post(
     *     path="/api/game/create",
     *     tags ={"Game"},
     *     summary = "Game create",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="username",
     *                     description="User name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="user_age",
     *                     description="User age",
     *                     type="integer"
     *                 ),
     *                 required={"username","user_age"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *        response=201,
     *        description="Game created"
     *     ),
     *     @OA\Response(
     *        response=400,
     *        description="Input erros"
     *     ),
     *     @OA\Response(
     *       response = "default",
     *      description = "An error occurred"
     *    )
     * )
     * */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|alpha:ascii|unique:games,username|max:20',
            'user_age' => 'required|digits_between:1,2',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $username = $request->username;
        $age = $request->user_age;
        $game_id = $this->game->createGame($username,$age);

        return response()->json([
            'game_id' => $game_id
        ],201);
    }

    /**
     *
     * * @OA\Delete (
     *     path="/api/game/delete/{id}",
     *     tags ={"Game"},
     *     summary = "Delete a game",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *          @OA\Examples( example = "int", value = "1", summary = "Enter a game id")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Game delete successfully"
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="The game has been not found"
     *      ),
     *      @OA\Response(
     *       response = "default",
     *      description = "An error occurred"
     *    )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $deleted = $this->game->deleteGame($id);
        if($deleted)
        {
            return response()->json([
                'message' => 'Game deleted successfully',
            ],200);
        }
        else
        {
            return response()->json([
                'message' => 'The game has been not found',
            ],404);
        }
    }
}
