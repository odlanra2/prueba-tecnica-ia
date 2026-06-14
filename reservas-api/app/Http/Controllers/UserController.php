<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
/**
 * @OA\Get(
 *     path="/api/users",
 *     summary="Listar usuarios",
 *     description="Devuelve todos los usuarios con sus datos básicos y reservas activas.",
 *     tags={"Usuarios"},
 *     @OA\Response(
 *         response=200,
 *         description="Lista de usuarios",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Juan Pérez"),
 *                 @OA\Property(property="email", type="string", example="juan@example.com"),
 *                 @OA\Property(property="plan", type="string", example="standard"),
 *                 @OA\Property(property="reservas_activas", type="integer", example=2)
 *             )
 *         )
 *     )
 * )
 */

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount([
        'reservations as reservas_activas' => function ($query) {
            $query->where('estado', 'active')
                  ->where('fecha_inicio', '>', now());
        }
        ])->get();

       return response()->json($users);
    }

    public function store(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json($user, 201);
    }
}
