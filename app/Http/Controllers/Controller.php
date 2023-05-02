<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="0.0.1",
 *      title="Bulls and Cows game",
 *      description="Game API"
 * )
 **/
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
