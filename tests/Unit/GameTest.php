<?php

namespace Tests\Unit;

use App\Game\BullCowGame;
use App\Repository\GameRepository;
use PHPUnit\Framework\TestCase;
use App\Models\Game;

class GameTest extends TestCase
{
    /**
     * A basic unit test example.
     */

    private BullCowGame $game_obj;

    public function setUp(): void
    {
        $game_model = new Game();
        $gameRepository_obj = new GameRepository($game_model);
        $this->game_obj = new BullCowGame($gameRepository_obj);
    }
    /*
     * Prueba para comprobar el correcto funcionamiento de
     * validCombination usada para comprobar que el usuario introduce
     * una cadena válida según las reglas que se establecen
     * */
    public function test_validateCombination(): void
    {
        //Comprobar que retorna falso cuando la cadena tiene valores repetidos
        $this->assertEquals(false,$this->game_obj->validCombination('1125'));
        $this->assertEquals(false,$this->game_obj->validCombination('1015'));
        $this->assertEquals(false,$this->game_obj->validCombination('1031'));
        $this->assertEquals(false,$this->game_obj->validCombination('3117'));
        $this->assertEquals(false,$this->game_obj->validCombination('3171'));
        $this->assertEquals(false,$this->game_obj->validCombination('3911'));

        //Comprobar que retorna true cuando todos son distintos
        $this->assertEquals(true,$this->game_obj->validCombination('1756'));
        $this->assertEquals(true,$this->game_obj->validCombination('8096'));
        $this->assertEquals(true,$this->game_obj->validCombination('0395'));
    }
    /*
     * Prueba para validar el funcionamiento de generateSecret
     * generando una cadena secreta y validada con validCombination
     * */
    public function test_generateSecret(): void
    {
        $this->assertEquals(true,$this->game_obj->validCombination($this->game_obj->generateSecret()));
    }

    /*
     * Prueba para validar el correcto funcionamiento de computeBullsCows
     * */
    public function test_computeBullsCows(): void
    {
        $this->assertEquals(['bulls' => 0, 'cows' => 0],$this->game_obj->computeBullsCows("6352","1908"));
        $this->assertEquals(['bulls' => 0, 'cows' => 2],$this->game_obj->computeBullsCows("6352","2069"));
        $this->assertEquals(['bulls' => 2, 'cows' => 0],$this->game_obj->computeBullsCows("1756","1853"));
        $this->assertEquals(['bulls' => 2, 'cows' => 2],$this->game_obj->computeBullsCows("8630","8360"));
        $this->assertEquals(['bulls' => 1, 'cows' => 2],$this->game_obj->computeBullsCows("9538","8583"));
        $this->assertEquals(['bulls' => 0, 'cows' => 4],$this->game_obj->computeBullsCows("0231","1023"));
        $this->assertEquals(['bulls' => 4, 'cows' => 0],$this->game_obj->computeBullsCows("6321","6321"));
    }

}
