<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerSnapshot extends Model
{
    protected $table = 'players_snapshot';

    protected $fillable = [
        'player_id', 'player_key', 'first_side', 'current_side',
        'nb_kill', 'assist', 'death', 'point', 'hs', 'defuse', 'bombe', 'tk',
        'nb1', 'nb2', 'nb3', 'nb4', 'nb5',
        'nb1kill', 'nb2kill', 'nb3kill', 'nb4kill', 'nb5kill',
        'pluskill', 'firstkill', 'round_id',
    ];

    protected $casts = [
        'nb_kill'   => 'integer', 'assist'    => 'integer', 'death'    => 'integer',
        'point'     => 'integer', 'hs'        => 'integer', 'defuse'   => 'integer',
        'bombe'     => 'integer', 'tk'        => 'integer', 'round_id' => 'integer',
        'nb1'       => 'integer', 'nb2'       => 'integer', 'nb3'      => 'integer',
        'nb4'       => 'integer', 'nb5'       => 'integer',
        'nb1kill'   => 'integer', 'nb2kill'   => 'integer', 'nb3kill'  => 'integer',
        'nb4kill'   => 'integer', 'nb5kill'   => 'integer',
        'pluskill'  => 'integer', 'firstkill' => 'integer',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player_id');
    }
}
