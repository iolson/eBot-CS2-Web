<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeasonFactory extends Factory
{
    public function definition(): array
    {
        $start = Carbon::now()->subMonths(rand(1, 6));
        $end   = $start->copy()->addMonths(rand(1, 6));

        return [
            'name'   => $this->faker->words(2, true) . ' Season',
            'event'  => $this->faker->words(3, true),
            'start'  => $start,
            'end'    => $end,
            'link'   => $this->faker->optional()->url(),
            'logo'   => null,
            'active' => $this->faker->boolean(70),
        ];
    }

    public function active(): static
    {
        return $this->state(['active' => true]);
    }
}
