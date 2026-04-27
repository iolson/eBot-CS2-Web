<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ip'       => $this->faker->ipv4() . ':27015',
            'rcon'     => $this->faker->password(8, 12),
            'hostname' => $this->faker->company() . ' CS2 Server',
            'tv_ip'    => $this->faker->optional()->ipv4(),
        ];
    }
}
