<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Service;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'duration' => $this->faker->numberBetween(30, 120),
            'price' => $this->faker->numberBetween(40000, 150000),
            'non_refundable' => false,  // 20% no reembolsables
        ];
    }
}
