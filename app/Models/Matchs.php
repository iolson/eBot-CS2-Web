<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * eBot match record.
 *
 * Note: Class is named `Matchs` (not `Match`) because `match` is a reserved
 * keyword in PHP 8+. The table name `matchs` is intentional in eBot's schema.
 */
class Matchs extends Model
{
    use HasFactory;

    /** @var string Intentional non-standard plural — mirrors the eBot database table name */
    protected $table = 'matchs';

    protected $fillable = [
        'ip', 'server_id', 'event_id', 'team_a', 'team_a_flag', 'team_a_name',
        'team_b', 'team_b_flag', 'team_b_name', 'status', 'is_paused',
        'score_a', 'score_b', 'max_round', 'rules', 'overtime_startmoney',
        'overtime_max_round', 'config_full_score', 'config_ot', 'config_streamer',
        'config_knife_round', 'config_switch_auto', 'config_auto_change_password',
        'config_password', 'config_heatmap', 'config_authkey', 'enable',
        'map_selection_mode', 'ingame_enable', 'current_map', 'force_zoom_match',
        'identifier_id', 'startdate', 'auto_start', 'auto_start_time',
    ];

    protected $casts = [
        'status' => 'integer',
        'is_paused' => 'boolean',
        'score_a' => 'integer',
        'score_b' => 'integer',
        'max_round' => 'integer',
        'overtime_startmoney' => 'integer',
        'overtime_max_round' => 'integer',
        'config_full_score' => 'boolean',
        'config_ot' => 'boolean',
        'config_streamer' => 'boolean',
        'config_knife_round' => 'boolean',
        'config_switch_auto' => 'boolean',
        'config_auto_change_password' => 'boolean',
        'config_heatmap' => 'boolean',
        'enable' => 'boolean',
        'ingame_enable' => 'boolean',
        'force_zoom_match' => 'boolean',
        'auto_start' => 'boolean',
        'auto_start_time' => 'integer',
        'startdate' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Status constants — must match eBot Node.js expectations exactly
    // -------------------------------------------------------------------------

    const STATUS_NOT_STARTED = 0;

    const STATUS_STARTING = 1;

    const STATUS_WU_KNIFE = 2;

    const STATUS_KNIFE = 3;

    const STATUS_END_KNIFE = 4;

    const STATUS_WU_1_SIDE = 5;

    const STATUS_FIRST_SIDE = 6;

    const STATUS_WU_2_SIDE = 7;

    const STATUS_SECOND_SIDE = 8;

    const STATUS_WU_OT_1_SIDE = 9;

    const STATUS_OT_FIRST_SIDE = 10;

    const STATUS_WU_OT_2_SIDE = 11;

    const STATUS_OT_SECOND_SIDE = 12;

    const STATUS_END_MATCH = 13;

    const STATUS_ARCHIVE = 14;

    const MAP_SELECTION_BO2 = 'bo2';

    const MAP_SELECTION_BO3_MODEA = 'bo3_modea';

    const MAP_SELECTION_BO3_MODEB = 'bo3_modeb';

    const MAP_SELECTION_NORMAL = 'normal';

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'server_id');
    }

    public function teamA(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_a');
    }

    public function teamB(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_b');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function currentMap(): BelongsTo
    {
        return $this->belongsTo(GameMap::class, 'current_map');
    }

    public function maps(): HasMany
    {
        return $this->hasMany(GameMap::class, 'match_id');
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class, 'match_id');
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(Round::class, 'match_id');
    }

    public function roundSummaries(): HasMany
    {
        return $this->hasMany(RoundSummary::class, 'match_id');
    }

    public function playerKills(): HasMany
    {
        return $this->hasMany(PlayerKill::class, 'match_id');
    }

    public function heatmapEntries(): HasMany
    {
        return $this->hasMany(PlayerHeatmap::class, 'match_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeLive($query)
    {
        return $query->where('enable', true)
            ->where('status', '>', self::STATUS_NOT_STARTED)
            ->where('status', '<', self::STATUS_END_MATCH);
    }

    public function scopeNotStarted($query)
    {
        return $query->where('status', self::STATUS_NOT_STARTED);
    }

    public function scopeFinished($query)
    {
        return $query->where('status', self::STATUS_END_MATCH);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', self::STATUS_ARCHIVE);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '<', self::STATUS_ARCHIVE);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getStatusText(): string
    {
        return match ($this->status) {
            self::STATUS_NOT_STARTED => 'Not started',
            self::STATUS_STARTING => 'Starting',
            self::STATUS_WU_KNIFE => 'Warmup Knife',
            self::STATUS_KNIFE => 'Knife Round',
            self::STATUS_END_KNIFE => 'Waiting choose team',
            self::STATUS_WU_1_SIDE => 'Warmup first side',
            self::STATUS_FIRST_SIDE => 'First side',
            self::STATUS_WU_2_SIDE => 'Warmup second side',
            self::STATUS_SECOND_SIDE => 'Second side',
            self::STATUS_WU_OT_1_SIDE => 'Warmup first side OT',
            self::STATUS_OT_FIRST_SIDE => 'First side OT',
            self::STATUS_WU_OT_2_SIDE => 'Warmup second side OT',
            self::STATUS_OT_SECOND_SIDE => 'Second side OT',
            self::STATUS_END_MATCH => 'Finished',
            self::STATUS_ARCHIVE => 'Archived',
            default => 'Unknown',
        };
    }

    public function isLive(): bool
    {
        return $this->enable &&
               $this->status > self::STATUS_NOT_STARTED &&
               $this->status < self::STATUS_END_MATCH;
    }

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVE;
    }

    public function getNbRound(): int
    {
        return (int) $this->score_a + (int) $this->score_b + 1;
    }
}
