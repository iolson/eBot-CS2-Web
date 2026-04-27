<?php

namespace Database\Factories;

use App\Models\GameMap;
use App\Models\Matchs;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<GameMap> */
class GameMapFactory extends Factory
{
    protected $model = GameMap::class;

    public function definition(): array
    {
        return [
            'match_id' => Matchs::factory(),
            'map_name' => $this->faker->randomElement([
                'de_dust2', 'de_inferno', 'de_mirage', 'de_nuke', 'de_overpass',
            ]),
            'score_1' => 0,
            'score_2' => 0,
            'current_side' => GameMap::SIDE_CT,
            'status' => 0,
            'maps_for' => GameMap::FOR_DEFAULT,
            'nb_ot' => 0,
            'identifier_id' => null,
            'tv_record_file' => null,
        ];
    }
}
