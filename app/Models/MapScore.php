<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MapScore extends Model
{
    protected $table = 'maps_score';

    protected $fillable = [
        'map_id', 'type_score', 'score1_side1', 'score1_side2',
        'score2_side1', 'score2_side2',
    ];

    protected $casts = [
        'score1_side1' => 'integer',
        'score1_side2' => 'integer',
        'score2_side1' => 'integer',
        'score2_side2' => 'integer',
    ];

    const TYPE_NORMAL = 'normal';

    const TYPE_OT = 'ot';

    public function map(): BelongsTo
    {
        return $this->belongsTo(GameMap::class, 'map_id');
    }
}
