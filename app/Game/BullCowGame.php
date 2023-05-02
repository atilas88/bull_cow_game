<?php

namespace App\Game;
use App\Repository\GameRepository;
use App\Models\Game;
class BullCowGame
{
    private GameRepository $game_repository;


    public function __construct($game_repository)
    {
        $this->game_repository = $game_repository;
    }


    public function createGame(string $username, int $user_age): int
    {
        $game = new Game;
        $game->username = $username;
        $game->user_age = $user_age;
        $game->status = 0;
        $game->secret = $this->generateSecret();
        $game->attemp = 0;
        $game->game_time = $this->setGameTime();
        $game->evaluation = 0;
        $this->game_repository->save($game);
        return $game->id;
    }
    /*
     * Helper para establecer el tiempo inicial del juego
     * */
    private function setGameTime(): int
    {
        return time() + env('MAX_GAME_TIME');
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
    /*
     * Helper para generar la combinación secreta
     * */

    private function generateSecret(): string
    {
        $secret = "";
        for ($i = 0; $i < 4; $i++) {
            $random_value = mt_rand(0, 9);
            while (str_contains($secret, "$random_value")) {
                $random_value = mt_rand(0, 9);
            }
            $secret .= "$random_value";
        }
        return $secret;
    }

    /*
     * Helper para calcular los toros y vacas
     * */
    private function computeBullsCows(string $combination,string $secret): array
    {
        $bulls = 0;
        $cows = 0;
        for ($i = 0; $i < strlen($combination); $i++) {
            $char = substr($combination, $i, 1);
            $pos = strpos($secret, $char);
            if ($pos !== false && $pos == $i) {
                $bulls++;
            }
            if ($pos !== false && $pos != $i) {
                $cows++;
            }
        }
        return ['bulls' => $bulls, 'cows' => $cows];
    }

    /*
     * Helper para comprobar el tiempo disponible
     * */
    public function checkGameTime($game)
    {
        return time() - $game->game_time;
    }
    /*
     * Función para evaluar las combinaciones
     * */
    public function analyzeCombination(string $combination,int $id): array
    {
      $game = $this->game_repository->get($id);
      $response = [];
      if(is_null($game))
      {
          $response['errors']['message'] = "Game not found";
          $response['errors']['code'] = 404;
      }
      else
      {

          $available_time = $this->checkGameTime($game);
          if($available_time >= 0)
          {
              $response['errors']['message'] = ['message'=>'Game Over','combination'=>$combination];
              $response['errors']['code'] = 408;
          }
          else
          {
              $available_time *= -1;
              $attemp = $game->attemp;
              $bulls_cows = $this->computeBullsCows($combination,$game->secret);
              $evaluation = $available_time / 2 + $attemp;
              $attemp++;
              $response['info']['content'] = ['combination' => $combination,
                                              'attemp'=>$attemp,
                                              'available_time'=>$available_time.' seconds',
                                              'bulls'=>$bulls_cows['bulls'],
                                              'cows' => $bulls_cows['cows'],
                                              'evaluation' => $evaluation,
                                              'rank' => 1
                                             ];
              $response['info']['code'] = 200;
          }
      }
      return $response;
    }
}
