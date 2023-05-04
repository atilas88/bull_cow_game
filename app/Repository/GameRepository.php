<?php

namespace App\Repository;
use App\Models\Game;
class GameRepository extends BaseRepository
{
    public function __construct(Game $game)
    {
        parent::__construct($game);
    }
    public function getGamesSorted()
    {
        return $this->model
                ->orderBy('status')
                ->orderBy('evaluation')
                ->get();
    }

}
