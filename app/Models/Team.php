<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $table = 'teams';

    protected $fillable = ['name', 'shorthandle', 'flag', 'link'];

    public function matchesAsTeamA(): HasMany
    {
        return $this->hasMany(Matchs::class, 'team_a');
    }

    public function matchesAsTeamB(): HasMany
    {
        return $this->hasMany(Matchs::class, 'team_b');
    }

    public function teamsInSeasons(): HasMany
    {
        return $this->hasMany(TeamInSeason::class, 'team_id');
    }

    public function seasons(): BelongsToMany
    {
        return $this->belongsToMany(Season::class, 'teams_in_seasons', 'team_id', 'season_id');
    }
}
