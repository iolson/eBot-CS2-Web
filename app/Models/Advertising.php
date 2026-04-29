<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Advertising extends Model
{
    use HasFactory;

    protected $table = 'advertising';

    protected $fillable = ['event_id', 'message', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
