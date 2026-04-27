<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Advertising extends Model
{
    protected $table = 'advertising';

    protected $fillable = ['season_id', 'message', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class, 'season_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
