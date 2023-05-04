<?php

namespace App\Game;
use App\Repository\GameRepository;
use App\Models\Game;
use Illuminate\Support\Facades\Cache;

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
        $game->status = 2;
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
     * Helper para validar que la combinación no tenga
     * caracteres repetidos
     * */
    public function validCombination($combination): bool
    {
        for ($i = 0 ; $i < strlen($combination); $i++)
        {
            $char = substr($combination, $i, 1);
            $str_to_search = substr($combination, $i + 1);
            if(str_contains($str_to_search, $char))
                return false;
        }
        return true;
    }
    /*
     * Helper para determinar si una combinación fue enviada
     * anteriormente
     * */
    public function checkDuplicateCombination($combination): bool
    {
        if (Cache::has("$combination"))
            return true;
        return false;
    }

    /*
     * Helper para calcular el rango segun la evaluacion
     * */
    public function computeRank($id): int
    {
        $number = 0;
        $games_sorted = $this->game_repository->getGamesSorted();
        foreach ($games_sorted as $key => $item)
        {
            if($item->id == $id)
            {
                $number = $key;
                break;
            }
        }
        return $number;
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
      $game_max_time = env('MAX_GAME_TIME');
      $game = $this->game_repository->get($id); //Buscar el juego
      $response = [];
      if(is_null($game))
      {
          $response['errors']['message'] = "Game not found";
          $response['errors']['code'] = 404;
      }
      else
      {
          $attemp = $game->attemp;
          $attemp++;
          $available_time = $this->checkGameTime($game);
          $game->attemp = $attemp;

          if($available_time >= 0)  //Juego perdido
          {
              $game->status = 3;
              $response['errors']['message'] = ['message'=>'Game Over','combination'=>$combination];
              $response['errors']['code'] = 408;
          }
          else
          {
              $available_time *= -1;
              $spent_time = $game_max_time - $available_time;
              $evaluation = $spent_time / 2 + $attemp;
              $game->evaluation = $evaluation;
              if(!$this->validCombination($combination) || $this->checkDuplicateCombination($combination))
              {
                  $response['errors']['message'] = ['message'=>'Duplicate value in combination or duplicate combination','combination'=>$combination];
                  $response['errors']['code'] = 400;
              }
              else if($combination == $game->secret) //juego ganado
              {
                  $game->status = 1;
                  $response['info']['content'] = ["Congratulations you have won with the combination: $combination"];
              }
              else
              {
                  $bulls_cows = $this->computeBullsCows($combination,$game->secret);
                  $game_info = ['combination' => $combination,
                      'attemp'=>$attemp,
                      'available_time'=>$available_time.' seconds',
                      'bulls'=>$bulls_cows['bulls'],
                      'cows' => $bulls_cows['cows'],
                      'evaluation' => $evaluation,
                      'rank' => $this->computeRank($id)
                  ];
                  $response['info']['content'] = $game_info;
                  Cache::put("$combination",$combination,$game_max_time);
                  Cache::put("$attemp",$game_info,$game_max_time);
              }
              $response['info']['code'] = 200;

          }
          $this->game_repository->save($game);
      }
      return $response;
    }
}
