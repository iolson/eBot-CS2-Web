<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin user is created by `php artisan ebot:install`, not here.
        if (app()->environment('local', 'testing')) {

            // Using BHOP April 2026 as Seed Data
            $event = Event::factory()->active()->create(['name' => 'BHOP']);

            // No Servers to Test Local LAN Servers
            $teamNames = ['Homecoming', 'Mock 2uh', 'rondon', 'Vantage Point', 'UltimateDarklordWizardz', 'Beer League Brawlers', 'Bot Squad', 'Saltcrew', 'IBUYSHOWER', 'Malort', 'Drunk Lawyers', 'Bot Brigade'];
            $createdTeams = [];
            foreach ($teamNames as $name) {
                $createdTeams[] = Team::factory()->create(['name' => $name]);
            }

            // Attach teams to event
            $event->teams()->attach(collect($createdTeams)->pluck('id')->toArray());
        }
    }
}
