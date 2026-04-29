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

    public function teamsInEvents(): HasMany
    {
        return $this->hasMany(TeamInEvent::class, 'team_id');
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'teams_in_events', 'team_id', 'event_id');
    }
}
