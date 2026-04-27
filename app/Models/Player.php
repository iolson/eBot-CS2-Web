<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model
{
    use HasFactory;

    protected $table = 'players';

    protected $fillable = [
        'match_id', 'map_id', 'player_key', 'team', 'ip', 'steamid',
        'first_side', 'current_side', 'pseudo',
        'nb_kill', 'assist', 'death', 'point', 'hs', 'defuse', 'bombe', 'tk',
        'nb1', 'nb2', 'nb3', 'nb4', 'nb5',
        'nb1kill', 'nb2kill', 'nb3kill', 'nb4kill', 'nb5kill',
        'pluskill', 'firstkill',
    ];

    protected $casts = [
        'nb_kill'   => 'integer', 'assist'   => 'integer', 'death'    => 'integer',
        'point'     => 'integer', 'hs'       => 'integer', 'defuse'   => 'integer',
        'bombe'     => 'integer', 'tk'       => 'integer',
        'nb1'       => 'integer', 'nb2'      => 'integer', 'nb3'      => 'integer',
        'nb4'       => 'integer', 'nb5'      => 'integer',
        'nb1kill'   => 'integer', 'nb2kill'  => 'integer', 'nb3kill'  => 'integer',
        'nb4kill'   => 'integer', 'nb5kill'  => 'integer',
        'pluskill'  => 'integer', 'firstkill' => 'integer',
    ];

    const TEAM_A     = 'a';
    const TEAM_B     = 'b';
    const TEAM_OTHER = 'other';

    const SIDE_CT    = 'ct';
    const SIDE_T     = 't';
    const SIDE_OTHER = 'other';

    public function match(): BelongsTo
    {
        return $this->belongsTo(Matchs::class, 'match_id');
    }

    public function map(): BelongsTo
    {
        return $this->belongsTo(GameMap::class, 'map_id');
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(PlayerSnapshot::class, 'player_id');
    }

    public function scopeTeamA($query)
    {
        return $query->where('team', self::TEAM_A);
    }

    public function scopeTeamB($query)
    {
        return $query->where('team', self::TEAM_B);
    }

    public function getKdRatio(): float
    {
        return $this->death > 0
            ? round($this->nb_kill / $this->death, 2)
            : (float) $this->nb_kill;
    }

    public function getHsPercent(): float
    {
        return $this->nb_kill > 0
            ? round(($this->hs / $this->nb_kill) * 100, 1)
            : 0.0;
    }
}
