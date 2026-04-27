<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerKill extends Model
{
    protected $table = 'player_kill';

    protected $fillable = [
        'match_id', 'map_id', 'killer_name', 'killer_id', 'killer_team',
        'killed_name', 'killed_id', 'killed_team', 'weapon', 'headshot', 'round_id',
    ];

    protected $casts = [
        'headshot' => 'boolean',
        'round_id' => 'integer',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(Matchs::class, 'match_id');
    }

    public function map(): BelongsTo
    {
        return $this->belongsTo(GameMap::class, 'map_id');
    }

    public function killer(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'killer_id');
    }

    public function killed(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'killed_id');
    }
}
