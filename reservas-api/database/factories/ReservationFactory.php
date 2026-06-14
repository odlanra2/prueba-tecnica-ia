<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Service;
use Carbon\Carbon;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition()
    {
        $start = Carbon::now()->addHours(5);

        return [
            'user_id' => User::factory(),
            'service_id' => Service::factory(),
            'fecha_inicio' => $start,
            'fecha_fin' => $start->copy()->addMinutes(60),
            'estado' => 'active',
        ];
    }
}
