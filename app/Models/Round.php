<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Round extends Model
{
    protected $table = 'round';

    protected $fillable = [
        'match_id', 'map_id', 'event_name', 'event_text',
        'event_time', 'kill_id', 'round_id',
    ];

    protected $casts = [
        'event_time' => 'integer',
        'kill_id'    => 'integer',
        'round_id'   => 'integer',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(Matchs::class, 'match_id');
    }

    public function map(): BelongsTo
    {
        return $this->belongsTo(GameMap::class, 'map_id');
    }

    public function kill(): BelongsTo
    {
        return $this->belongsTo(PlayerKill::class, 'kill_id');
    }
}
