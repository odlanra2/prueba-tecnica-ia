<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $json = File::get(database_path('data/seed.json'));
        $data = json_decode($json, true);

        foreach ($data['users'] as $u) {
            User::create([
                'name' => $u['name'],
                'email' => $u['email'],
                'password' => bcrypt($u['password']),
                'plan' => $u['plan'],
            ]);
        }
    }
}


