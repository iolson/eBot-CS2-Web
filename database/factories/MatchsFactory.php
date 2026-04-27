<?php

namespace Database\Factories;

use App\Models\Matchs as MatchModel;
use App\Models\Season;
use App\Models\Server;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MatchModel> */
class MatchsFactory extends Factory
{
    protected $model = MatchModel::class;

    public function definition(): array
    {
        $teamAName = substr($this->faker->company(), 0, 25);
        $teamBName = substr($this->faker->company(), 0, 25);

        return [
            'ip' => $this->faker->ipv4().':27015',
            'server_id' => Server::factory(),
            'season_id' => Season::factory(),
            'team_a' => Team::factory(),
            'team_a_flag' => $this->faker->optional()->countryCode(),
            'team_a_name' => $teamAName,
            'team_b' => Team::factory(),
            'team_b_flag' => $this->faker->optional()->countryCode(),
            'team_b_name' => $teamBName,
            'status' => MatchModel::STATUS_NOT_STARTED,
            'is_paused' => false,
            'score_a' => 0,
            'score_b' => 0,
            'max_round' => 12,
            'rules' => null,
            'overtime_startmoney' => 10000,
            'overtime_max_round' => 3,
            'config_full_score' => false,
            'config_ot' => true,
            'config_streamer' => false,
            'config_knife_round' => false,
            'config_switch_auto' => false,
            'config_auto_change_password' => false,
            'config_password' => null,
            'config_heatmap' => false,
            'config_authkey' => null,
            'enable' => true,
            'map_selection_mode' => MatchModel::MAP_SELECTION_NORMAL,
            'ingame_enable' => false,
            'current_map' => null,
            'force_zoom_match' => false,
            'identifier_id' => null,
            'startdate' => null,
            'auto_start' => false,
            'auto_start_time' => null,
        ];
    }

    public function notStarted(): static
    {
        return $this->state(['status' => MatchModel::STATUS_NOT_STARTED]);
    }

    public function live(): static
    {
        return $this->state([
            'status' => MatchModel::STATUS_FIRST_SIDE,
            'enable' => true,
        ]);
    }

    public function finished(): static
    {
        return $this->state([
            'status' => MatchModel::STATUS_END_MATCH,
            'score_a' => $this->faker->numberBetween(10, 16),
            'score_b' => $this->faker->numberBetween(0, 14),
        ]);
    }

    public function archived(): static
    {
        return $this->state(['status' => MatchModel::STATUS_ARCHIVE]);
    }
}
