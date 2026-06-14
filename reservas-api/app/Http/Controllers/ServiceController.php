<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Contracts\ServiceServiceInterface;


class ServiceController extends Controller
{
    private $serviceService;

    public function __construct(ServiceServiceInterface $serviceService)
    {
        $this->serviceService = $serviceService;
    }
    /**
     * @OA\Get(
     *     path="/api/services",
     *     summary="Listar servicios",
     *     description="Devuelve todos los servicios disponibles con sus datos básicos.",
     *     tags={"Servicios"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de servicios",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Corte de cabello"),
     *                 @OA\Property(property="duration", type="integer", example=60),
     *                 @OA\Property(property="price", type="number", format="float", example=50000),
     *                 @OA\Property(property="non_refundable", type="boolean", example=false)
     *             )
     *         )
     *     )
     * )
     */

    public function index()
    {
        return response()->json($this->serviceService->all());
    }

    /**
     * @OA\Post(
     *     path="/api/services",
     *     summary="Crear servicio",
     *     description="Crea un nuevo servicio con nombre, duración, precio y si es reembolsable o no.",
     *     tags={"Servicios"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","duration","price"},
     *             @OA\Property(property="name", type="string", example="Corte de cabello"),
     *             @OA\Property(property="duration", type="integer", example=60),
     *             @OA\Property(property="price", type="number", format="float", example=50000),
     *             @OA\Property(property="non_refundable", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Servicio creado",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Corte de cabello"),
     *             @OA\Property(property="duration", type="integer", example=60),
     *             @OA\Property(property="price", type="number", format="float", example=50000),
     *             @OA\Property(property="non_refundable", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */


    public function store(Request $request)
    {
        $service = $this->serviceService->create($request->all());
        return response()->json($service, 201);
    }

    public function update(Request $request, $id)
    {
        $service = $this->serviceService->update($id, $request->all());
        return response()->json($service);
    }

    public function destroy($id)
    {
        $this->serviceService->delete($id);
        return response()->json(['message' => 'Service deleted successfully']);
    }
}
