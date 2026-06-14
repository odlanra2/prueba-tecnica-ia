<?php

namespace App\Services;

use App\Services\Contracts\ReservationServiceInterface;
use App\Models\Reservation;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;

class ReservationService implements ReservationServiceInterface
{


    public function all()
    {
        return Reservation::all();
    }



   public function create(array $data): Reservation
        {
            $user = User::findOrFail($data['user_id']);
            $service = Service::findOrFail($data['service_id']);
            $fechaInicio = Carbon::parse($data['fecha_inicio'], 'America/Bogota');
            $fechaFin = $fechaInicio->copy()->addMinutes($service->duration);

            // 1. Horarios de operación (lunes-sábado, 7:00–19:00, no domingos ni festivos)
            if ($fechaInicio->isSunday()) {
                throw new \Exception('No se permiten reservas los domingos.');
            }
            if ($this->esFestivo($fechaInicio)) {
                throw new \Exception('No se permiten reservas en festivos.');
            }
            if ((int)$fechaInicio->hour < 7 || (int)$fechaInicio->hour >= 19) {
                throw new \Exception("La reserva debe estar entre 7:00 y 19:00.  $fechaInicio->hour");
            }
            $ahora = Carbon::now('America/Bogota');
            $diffMinutos = $ahora->diffInMinutes($fechaInicio, false);
            // 2. Anticipación mínima de 2 horas
           if ($diffMinutos < 120) {
               throw new \Exception('La reserva debe hacerse con al menos 2 horas de anticipación.');
            }
            // 3. Máximo 3 reservas activas por usuario
            $reservasActivas = Reservation::where('user_id', $user->id)
                ->where('estado', 'active')
                ->where('fecha_inicio', '>', Carbon::now())
                ->count();

            if ($reservasActivas >= 3) {
                throw new \Exception('El usuario ya tiene el máximo de 3 reservas activas.');
            }

            // 4. Evitar solapamiento de reservas para el mismo servicio
            $solapada = Reservation::where('service_id', $service->id)
                ->where('estado', 'active')
                ->where(function ($q) use ($fechaInicio, $fechaFin) {
                    $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                    ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin]);
                })
                ->exists();

            if ($solapada) {
                throw new \Exception('El servicio ya tiene una reserva en ese horario.');
            }

            // ✅ Si pasa todas las validaciones, crear la reserva
            return Reservation::create([
                'user_id' => $user->id,
                'service_id' => $service->id,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'estado' => 'active',
            ]);
        }

    public function cancel(int $id): array
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->estado = 'cancelled';
        $reservation->save();

        $reembolso = $this->calcularReembolso($reservation);

        return [
            'message' => 'Reserva cancelada',
            'reembolso' => $reembolso
        ];
    }

    public function list(int $userId, string $from, string $to): array
    {
      return Reservation::with('service')
        ->where('user_id', $userId)
        ->whereBetween('fecha_inicio', [$from, $to])
        ->get()
        ->toArray();
    }


    public function  calcularReembolso(Reservation $reservation): float
    {
        $service = $reservation->service;
        $user = $reservation->user;
        $now = Carbon::now();
        $diffHoras = $now->diffInHours(Carbon::parse($reservation->fecha_inicio), false);

        if ($service->non_refundable) return 0;

        if ($user->plan === 'premium') {
            if ($diffHoras >= 4) return $service->price;
            if ($diffHoras >= 1) return $service->price * 0.5;
            return 0;
        }

        if ($diffHoras > 24) return $service->price;
        if ($diffHoras >= 4) return $service->price * 0.5;
        return 0;
    }

        /**
         * Validación de festivos (ejemplo estático, puedes cargar de BD o API)
         */
       public function esFestivo(Carbon $fecha): bool
        {
            $festivos2026 = [
                '2026-01-01', '2026-03-23', '2026-03-29', '2026-03-30', '2026-05-01',
                '2026-07-20', '2026-08-07', '2026-12-25'
            ];
            return in_array($fecha->toDateString(), $festivos2026);
        }
}
