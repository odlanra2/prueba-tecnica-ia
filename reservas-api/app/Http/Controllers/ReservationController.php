<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Contracts\ReservationServiceInterface;

class ReservationController extends Controller
{
   private $reservationService;

    public function __construct(ReservationServiceInterface $reservationService)
    {
          $this->reservationService = $reservationService;
    }



    public function index()
    {
        $reservas = $this->reservationService->all();
        return response()->json($reservas);
    }


     /**
     * @OA\Post(
     *     path="/api/reservations",
     *     summary="Crear reserva",
     *     description="Permite a un usuario crear una reserva para un servicio, validando horarios y reglas de anticipación.",
     *     tags={"Reservas"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id","service_id","fecha_inicio"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="service_id", type="integer", example=2),
     *             @OA\Property(property="fecha_inicio", type="string", format="date-time", example="2026-06-15T10:00:00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Reserva creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=10),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="service_id", type="integer", example=2),
     *             @OA\Property(property="fecha_inicio", type="string", format="date-time", example="2026-06-15T10:00:00"),
     *             @OA\Property(property="fecha_fin", type="string", format="date-time", example="2026-06-15T11:00:00"),
     *             @OA\Property(property="estado", type="string", example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación (ej. menos de 2 horas de anticipación)"
     *     )
     * )
     */

    public function store(Request $request)
    {
        $reservation = $this->reservationService->create($request->all());
        return response()->json($reservation, 201);
    }

     /**
     * @OA\Post(
     *     path="/api/reservations/{id}/cancel",
     *     summary="Cancelar reserva",
     *     description="Cancela una reserva activa y devuelve el monto de reembolso según las reglas del servicio y plan del usuario.",
     *     tags={"Reservas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la reserva a cancelar",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reserva cancelada con reembolso calculado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="reembolso", type="number", format="float", example=50000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Reserva no encontrada"
     *     )
     * )
     */


    public function cancel($id)
    {
        $result = $this->reservationService->cancel($id);
        return response()->json($result);
    }


    /**
     * @OA\Get(
     *     path="/api/reservations",
     *     summary="Listar reservas",
     *     description="Devuelve las reservas filtradas por usuario y rango de fechas.",
     *     tags={"Reservas"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=false,
     *         description="ID del usuario",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="from",
     *         in="query",
     *         required=false,
     *         description="Fecha inicial del rango",
     *         @OA\Schema(type="string", format="date", example="2026-06-12")
     *     ),
     *     @OA\Parameter(
     *         name="to",
     *         in="query",
     *         required=false,
     *         description="Fecha final del rango",
     *         @OA\Schema(type="string", format="date", example="2026-06-30")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de reservas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=10),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="service_id", type="integer", example=2),
     *                 @OA\Property(property="fecha_inicio", type="string", format="date-time", example="2026-06-15T10:00:00"),
     *                 @OA\Property(property="fecha_fin", type="string", format="date-time", example="2026-06-15T11:00:00"),
     *                 @OA\Property(property="estado", type="string", example="active")
     *             )
     *         )
     *     )
     * )
     */

    public function list(Request $request)
    {
        $reservas = $this->reservationService->list(
            $request->user_id,
            $request->from,
            $request->to
        );
        return response()->json($reservas);
    }
}
