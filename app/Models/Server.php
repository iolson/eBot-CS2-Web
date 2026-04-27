<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Server extends Model
{
    use HasFactory;

    protected $table = 'servers';

    protected $fillable = ['ip', 'rcon', 'hostname', 'tv_ip'];

    public function matches(): HasMany
    {
        return $this->hasMany(Matchs::class, 'server_id');
    }

    public function getDisplayIp(): string
    {
        return config('ebot.mode') === 'lan' ? $this->ip : ($this->tv_ip ?: $this->ip);
    }
}
