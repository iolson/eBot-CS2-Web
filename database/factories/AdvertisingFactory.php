<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AdvertisingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => null,
            'message' => $this->faker->sentence(),
            'active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['active' => false]);
    }
}
