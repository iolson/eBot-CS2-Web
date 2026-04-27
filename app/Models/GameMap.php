<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Represents a map within a match (BO1/BO3/BO5).
 * Named GameMap to avoid collision with PHP's built-in Map concept.
 * Database table: maps
 */
class GameMap extends Model
{
    use HasFactory;

    protected $table = 'maps';

    protected $fillable = [
        'match_id', 'map_name', 'score_1', 'score_2', 'current_side',
        'status', 'maps_for', 'nb_ot', 'identifier_id', 'tv_record_file',
    ];

    protected $casts = [
        'score_1' => 'integer',
        'score_2' => 'integer',
        'status' => 'integer',
        'nb_ot' => 'integer',
        'identifier_id' => 'integer',
    ];

    const SIDE_CT = 'ct';

    const SIDE_T = 't';

    const FOR_DEFAULT = 'default';

    const FOR_TEAM1 = 'team1';

    const FOR_TEAM2 = 'team2';

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function match(): BelongsTo
    {
        return $this->belongsTo(Matchs::class, 'match_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(MapScore::class, 'map_id');
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class, 'map_id');
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(Round::class, 'map_id');
    }

    public function roundSummaries(): HasMany
    {
        return $this->hasMany(RoundSummary::class, 'map_id');
    }

    public function playerKills(): HasMany
    {
        return $this->hasMany(PlayerKill::class, 'map_id');
    }

    public function heatmapEntries(): HasMany
    {
        return $this->hasMany(PlayerHeatmap::class, 'map_id');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getDemoPath(): ?string
    {
        if (! $this->tv_record_file) {
            return null;
        }

        $demoPath = rtrim(config('ebot.demo_path'), '/');

        return "{$demoPath}/{$this->tv_record_file}.dem.zip";
    }

    public function hasDemoFile(): bool
    {
        $path = $this->getDemoPath();

        return $path !== null && file_exists($path);
    }
}
