<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoundSummary extends Model
{
    protected $table = 'round_summary';

    protected $fillable = [
        'match_id', 'map_id', 'bomb_planted', 'bomb_defused', 'bomb_exploded',
        'win_type', 'team_win', 'ct_win', 't_win', 'score_a', 'score_b',
        'best_killer', 'best_killer_nb', 'best_killer_fk',
        'best_action_type', 'best_action_param', 'backup_file_name', 'round_id',
    ];

    protected $casts = [
        'bomb_planted' => 'boolean',
        'bomb_defused' => 'boolean',
        'bomb_exploded' => 'boolean',
        'ct_win' => 'boolean',
        't_win' => 'boolean',
        'best_killer_fk' => 'boolean',
        'score_a' => 'integer',
        'score_b' => 'integer',
        'best_killer_nb' => 'integer',
        'round_id' => 'integer',
    ];

    const WIN_TYPE_BOMB_DEFUSED = 'bombdefused';

    const WIN_TYPE_BOMB_EXPLODED = 'bombeexploded';

    const WIN_TYPE_NORMAL = 'normal';

    const WIN_TYPE_SAVED = 'saved';

    public function match(): BelongsTo
    {
        return $this->belongsTo(Matchs::class, 'match_id');
    }

    public function map(): BelongsTo
    {
        return $this->belongsTo(GameMap::class, 'map_id');
    }

    public function bestKillerPlayer(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'best_killer');
    }
}
