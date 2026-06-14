<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Reservation;

class ReservationsTableSeeder extends Seeder
{
    public function run()
    {
        $json = File::get(database_path('data/seed.json'));
        $data = json_decode($json, true);

        foreach ($data['reservations'] as $r) {
            Reservation::create($r);
        }
    }
}
