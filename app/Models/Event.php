<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = ['name', 'event', 'start', 'end', 'link', 'logo', 'active'];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'active' => 'boolean',
    ];

    public function matches(): HasMany
    {
        return $this->hasMany(Matchs::class, 'event_id');
    }

    public function advertising(): HasMany
    {
        return $this->hasMany(Advertising::class, 'event_id');
    }

    public function teamsInEvents(): HasMany
    {
        return $this->hasMany(TeamInEvent::class, 'event_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'teams_in_events', 'event_id', 'team_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
