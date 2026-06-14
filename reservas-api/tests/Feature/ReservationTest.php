<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Service;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function no_permite_reservar_con_menos_de_2_horas_de_anticipacion()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['duration' => 60]);

        $fechaInicio = Carbon::now()->addMinutes(90); // 1h30 min
        $response = $this->actingAs($user)->postJson('/api/reservations', [
            'service_id' => $service->id,
            'fecha_inicio' => $fechaInicio,
        ]);

        $response->assertStatus(500); // o el código que uses para excepción
        $this->assertDatabaseCount('reservations', 0);
    }

    /** @test */
    public function no_permite_solapamiento_de_reservas()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['duration' => 60]);

        // Reserva existente
        Reservation::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'fecha_inicio' => Carbon::now()->addHours(5),
            'fecha_fin' => Carbon::now()->addHours(6),
            'estado' => 'active',
        ]);

        // Intento de solapamiento
        $response = $this->actingAs($user)->postJson('/api/reservations', [
            'service_id' => $service->id,
            'fecha_inicio' => Carbon::now()->addHours(5)->addMinutes(30),
        ]);

        $response->assertStatus(500);
    }

    /** @test */
    public function reembolso_estandar_es_100_por_ciento_si_cancela_con_mas_de_24_horas()
    {
        $user = User::factory()->create(['plan' => 'standard']);
        $service = Service::factory()->create(['price' => 100000]);

        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'fecha_inicio' => Carbon::now()->addHours(30),
            'estado' => 'active',
        ]);

        $reembolso = app('App\Services\ReservationService')->calcularReembolso($reservation);

        $this->assertEquals(100000, $reembolso);
    }

    /** @test */
    public function reembolso_tardia_es_50_por_ciento_si_cancela_entre_24_y_4_horas()
    {
        $user = User::factory()->create(['plan' => 'standard']);
        $service = Service::factory()->create(['price' => 100000, 'non_refundable' => false]);

        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'fecha_inicio' => Carbon::now()->addHours(12),
            'estado' => 'active',
        ]);

        $reembolso = app('App\Services\ReservationService')->calcularReembolso($reservation);

        $this->assertEquals(50000, $reembolso);
    }

    /** @test */
    public function reembolso_muy_tardia_es_0_si_cancela_con_menos_de_4_horas()
    {
        $user = User::factory()->create(['plan' => 'standard']);
        $service = Service::factory()->create(['price' => 100000]);

        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'fecha_inicio' => Carbon::now()->addHours(2),
            'estado' => 'active',
        ]);

        $reembolso = app('App\Services\ReservationService')->calcularReembolso($reservation);

        $this->assertEquals(0, $reembolso);
    }

    /** @test */
    public function usuarios_premium_tienen_reembolso_especial()
    {
        $user = User::factory()->create(['plan' => 'premium']);
        $service = Service::factory()->create(['price' => 100000]);

        // Caso 1: más de 4h → 100%
        $reservation1 = Reservation::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'fecha_inicio' => Carbon::now()->addHours(6),
            'estado' => 'active',
        ]);
        $this->assertEquals(100000, app('App\Services\ReservationService')->calcularReembolso($reservation1));

        // Caso 2: entre 4h y 1h → 50%
        $reservation2 = Reservation::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'fecha_inicio' => Carbon::now()->addHours(2),
            'estado' => 'active',
        ]);
        $this->assertEquals(50000, app('App\Services\ReservationService')->calcularReembolso($reservation2));

        // Caso 3: menos de 1h → 0%
        $reservation3 = Reservation::factory()->create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'fecha_inicio' => Carbon::now()->addMinutes(30),
            'estado' => 'active',
        ]);
        $this->assertEquals(0, app('App\Services\ReservationService')->calcularReembolso($reservation3));
    }
}
