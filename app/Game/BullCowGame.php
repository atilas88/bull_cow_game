<?php

namespace App\Game;
use App\Repository\GameRepository;
use App\Models\Game;
class BullCowGame
{
    private GameRepository $game_repository;
    private int $evaluation;
    private int $status;
    public function __construct($game_repository)
    {
        $this->evaluation = 0;
        $this->status = 0;
        $this->game_repository = $game_repository;
    }
    public function createGame(string $username, int $user_age): int
    {
        $game = new Game;
        $game->username = $username;
        $game->user_age = $user_age;
        $game->evaluation = $this->evaluation;
        $game->status = $this->status;
        $this->game_repository->save($game);
        return $game->id;
    }

    public function deleteGame(int $id): bool
    {
        $game = $this->game_repository->get($id);

        if (is_null($game)) {
            return false;
        } else {
            $this->game_repository->delete($game);
            return true;
        }
    }
}
