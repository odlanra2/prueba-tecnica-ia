<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Reservas API",
 *     description="Documentación de la API de reservas."
 * )
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Servidor local"
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
