<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    use HasFactory;

    protected $table = 'seasons';

    protected $fillable = ['name', 'event', 'start', 'end', 'link', 'logo', 'active'];

    protected $casts = [
        'start'  => 'datetime',
        'end'    => 'datetime',
        'active' => 'boolean',
    ];

    public function matches(): HasMany
    {
        return $this->hasMany(Matchs::class, 'season_id');
    }

    public function advertising(): HasMany
    {
        return $this->hasMany(Advertising::class, 'season_id');
    }

    public function teamsInSeasons(): HasMany
    {
        return $this->hasMany(TeamInSeason::class, 'season_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'teams_in_seasons', 'season_id', 'team_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
