<?php

namespace Database\Factories;

use App\Models\Matchs as MatchModel;
use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerFactory extends Factory
{
    protected $model = Player::class;

    public function definition(): array
    {
        $kills = $this->faker->numberBetween(0, 30);
        $deaths = $this->faker->numberBetween(1, 25);

        return [
            'match_id' => MatchModel::factory(),
            'map_id' => null,
            'player_key' => $this->faker->numerify('STEAM_0:0:#########'),
            'team' => $this->faker->randomElement([Player::TEAM_A, Player::TEAM_B]),
            'ip' => $this->faker->ipv4(),
            'steamid' => $this->faker->numerify('7656119##########'),
            'first_side' => $this->faker->randomElement([Player::SIDE_CT, Player::SIDE_T]),
            'current_side' => $this->faker->randomElement([Player::SIDE_CT, Player::SIDE_T]),
            'pseudo' => $this->faker->userName(),
            'nb_kill' => $kills,
            'assist' => $this->faker->numberBetween(0, 10),
            'death' => $deaths,
            'point' => $this->faker->numberBetween(0, 100),
            'hs' => $this->faker->numberBetween(0, $kills),
            'defuse' => $this->faker->numberBetween(0, 3),
            'bombe' => $this->faker->numberBetween(0, 3),
            'tk' => $this->faker->numberBetween(0, 2),
            'nb1' => 0,
            'nb2' => 0,
            'nb3' => 0,
            'nb4' => 0,
            'nb5' => 0,
            'nb1kill' => $this->faker->numberBetween(0, 10),
            'nb2kill' => $this->faker->numberBetween(0, 6),
            'nb3kill' => $this->faker->numberBetween(0, 3),
            'nb4kill' => $this->faker->numberBetween(0, 1),
            'nb5kill' => 0,
            'pluskill' => $this->faker->numberBetween(0, $kills),
            'firstkill' => $this->faker->numberBetween(0, 5),
        ];
    }

    public function teamA(): static
    {
        return $this->state(['team' => Player::TEAM_A]);
    }

    public function teamB(): static
    {
        return $this->state(['team' => Player::TEAM_B]);
    }
}
