<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'shorthandle' => strtoupper($this->faker->lexify('???')),
            'flag' => $this->faker->optional()->countryCode(),
            'link' => $this->faker->optional()->url(),
        ];
    }
}
