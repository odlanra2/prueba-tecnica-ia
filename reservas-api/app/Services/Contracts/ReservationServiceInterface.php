<?php

namespace App\Services\Contracts;

use App\Models\Reservation;

interface ReservationServiceInterface
{
    public function all();
    public function create(array $data): Reservation;
    public function cancel(int $id): array;
    public function list(int $userId, string $from, string $to): array;
}
