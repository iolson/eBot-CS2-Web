<?php

namespace Database\Seeders;

use App\Models\Matchs as MatchModel;
use App\Models\Season;
use App\Models\Server;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create a default admin user (password will be set during install wizard)
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);

        // Development-only sample data
        if (app()->environment('local', 'testing')) {
            $season = Season::factory()->active()->create(['name' => 'Demo Season']);
            $servers = Server::factory(3)->create();
            $teams = Team::factory(8)->create();

            // Attach teams to season
            $season->teams()->attach($teams->pluck('id'));

            // Create sample matches
            MatchModel::factory(5)
                ->recycle($servers)
                ->recycle($teams)
                ->create(['season_id' => $season->id]);

            MatchModel::factory(3)
                ->finished()
                ->recycle($servers)
                ->recycle($teams)
                ->create(['season_id' => $season->id]);
        }
    }
}
