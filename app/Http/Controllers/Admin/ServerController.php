<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function index()
    {
        $servers = Server::withCount('matches')->orderBy('ip')->paginate(25);

        return view('admin.servers.index', compact('servers'));
    }

    public function create()
    {
        return view('admin.servers.create');
    }

    /**
     * Store one or more servers from a batch IP/port definition.
     *
     * Supported formats (matching legacy eBot server module):
     *   192.168.1.1:27015          — single server
     *   192.168.1.1-10:27015       — IP range, single port (10 servers)
     *   192.168.1.1:27015-27020    — single IP, port range (6 servers)
     *   192.168.1.1-10:27015-27020 — IP range + port range
     *   hostname.example.com:27015 — hostname (no expansion)
     *
     * The `ip` column stores the full "ip:port" string.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'ip_port' => ['required', 'string', 'max:500'],
            'rcon'    => ['required', 'string', 'max:50'],
        ]);

        $entries = $this->parseIpPort($request->ip_port, $request->rcon);

        if (empty($entries)) {
            return back()->withInput()->with('error', __('Invalid IP/port format.'));
        }

        foreach ($entries as $entry) {
            Server::firstOrCreate(
                ['ip' => $entry['ip']],
                ['rcon' => $entry['rcon'], 'hostname' => $entry['ip']]
            );
        }

        $count = count($entries);

        return redirect()->route('admin.servers.index')
            ->with('success', __(':count server(s) added.', ['count' => $count]));
    }

    public function destroy(Server $server): RedirectResponse
    {
        if ($server->matches()->whereIn('status', range(1, 12))->exists()) {
            return back()->with('error', __('Cannot delete a server with an active match.'));
        }

        $server->delete();

        return redirect()->route('admin.servers.index')
            ->with('success', __('Server deleted.'));
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Parse batch IP/port input into an array of ['ip' => 'ip:port', 'rcon' => '...'].
     */
    private function parseIpPort(string $input, string $rcon): array
    {
        $input = trim($input);

        if (! preg_match('/^([^:]+):(\d+(?:-\d+)?)$/', $input, $m)) {
            return [];
        }

        $hostPart = $m[1];
        $portPart = $m[2];

        $ports = $this->expandRange($portPart, 1024, 65535);
        if (empty($ports)) {
            return [];
        }

        // Expand IP range (e.g. 192.168.1.1-10) or use hostname as-is
        $hosts = [];
        if (preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.)(\d{1,3})(?:-(\d{1,3}))?$/', $hostPart, $im)) {
            $prefix = $im[1];
            $start  = (int) $im[2];
            $end    = isset($im[3]) && $im[3] !== '' ? (int) $im[3] : $start;
            for ($i = $start; $i <= $end; $i++) {
                $hosts[] = $prefix . $i;
            }
        } else {
            $hosts[] = $hostPart;
        }

        $entries = [];
        foreach ($hosts as $host) {
            foreach ($ports as $port) {
                $entries[] = ['ip' => "{$host}:{$port}", 'rcon' => $rcon];
            }
        }

        return $entries;
    }

    private function expandRange(string $range, int $min, int $max): array
    {
        if (str_contains($range, '-')) {
            [$start, $end] = explode('-', $range, 2);
            $start = (int) $start;
            $end   = (int) $end;
            if ($start < $min || $end > $max || $start > $end) {
                return [];
            }

            return range($start, $end);
        }

        $val = (int) $range;
        if ($val < $min || $val > $max) {
            return [];
        }

        return [$val];
    }
}
