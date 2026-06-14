<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Service;

class ServicesTableSeeder extends Seeder
{
    public function run()
    {
        $json = File::get(database_path('data/seed.json'));
        $data = json_decode($json, true);

        foreach ($data['services'] as $s) {
            Service::create($s);
        }
    }
}
