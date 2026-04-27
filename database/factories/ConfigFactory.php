<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ConfigFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word().'_cfg',
            'content' => $this->faker->optional()->paragraph(),
        ];
    }
}
