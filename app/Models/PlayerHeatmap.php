<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerHeatmap extends Model
{
    protected $table = 'players_heatmap';

    protected $fillable = [
        'match_id', 'map_id', 'event_name',
        'event_x', 'event_y', 'event_z',
        'player_name', 'player_id', 'player_team',
        'attacker_x', 'attacker_y', 'attacker_z',
        'attacker_name', 'attacker_id', 'attacker_team',
        'round_id', 'round_time',
    ];

    protected $casts = [
        'event_x' => 'float', 'event_y' => 'float', 'event_z' => 'float',
        'attacker_x' => 'float', 'attacker_y' => 'float', 'attacker_z' => 'float',
        'round_id' => 'integer', 'round_time' => 'integer',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(Matchs::class, 'match_id');
    }

    public function map(): BelongsTo
    {
        return $this->belongsTo(GameMap::class, 'map_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function attacker(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'attacker_id');
    }
}
