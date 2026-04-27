<?php

namespace App\Services;

use App\Models\Matchs;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends encrypted lifecycle commands to the eBot Node.js game server.
 *
 * Command format (per legacy matchsInProgressSuccess.php):
 *   plaintext = "{matchId} {action} {serverIp}"
 *   encrypted = AES-CTR-256(plaintext, config_authkey)
 *   payload   = JSON([encrypted, serverIp])
 *
 * The payload is POSTed to the eBot Node.js server's /match-command endpoint.
 * If the server is unreachable the error is logged and the caller is notified
 * via the boolean return value.
 */
class EbotCommandService
{
    public function __construct(
        private readonly AesCtrService $aes,
        private readonly string $websocketUrl,
    ) {}

    /**
     * Send an encrypted command for an in-game match action.
     *
     * @param  string  $action  e.g. 'stop', 'stopback', 'pauseunpause', 'forcestart',
     *                          'forceknife', 'forceknifeend', 'passknife', 'skipmapnext', 'skipmapprev'
     */
    public function send(Matchs $match, string $action): bool
    {
        if (empty($match->config_authkey) || empty($match->ip)) {
            Log::warning("EbotCommandService: match {$match->id} has no authkey or server IP.");

            return false;
        }

        $plaintext = "{$match->id} {$action} {$match->ip}";
        $encrypted = $this->aes->encrypt($plaintext, $match->config_authkey, 256);
        $payload = json_encode([$encrypted, $match->ip]);

        try {
            $url = rtrim($this->websocketUrl, '/').'/match-command';

            $response = Http::timeout(5)->post($url, [
                'data' => $payload,
            ]);

            if (! $response->successful()) {
                Log::warning("EbotCommandService: server returned HTTP {$response->status()} for match {$match->id} action '{$action}'.");

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning("EbotCommandService: could not reach eBot server for match {$match->id} action '{$action}': {$e->getMessage()}");

            return false;
        }
    }

    /**
     * Build the encrypted command payload string (used by the browser Socket.IO client).
     * Returns [encryptedData, serverIp] as JSON.
     */
    public function buildPayload(Matchs $match, string $action): string
    {
        if (empty($match->config_authkey) || empty($match->ip)) {
            return json_encode(['', '']);
        }

        $plaintext = "{$match->id} {$action} {$match->ip}";
        $encrypted = $this->aes->encrypt($plaintext, $match->config_authkey, 256);

        return json_encode([$encrypted, $match->ip]);
    }
}
