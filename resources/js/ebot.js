/**
 * eBot CS2 Socket.IO client integration.
 *
 * Connects to the eBot Node.js server using the JWT token injected by the
 * PHP layout (window.ebotConfig.jwtToken). Updates match status indicators
 * in real time without requiring a page reload.
 *
 * Command format (from legacy matchsInProgressSuccess.php):
 *   plaintext = "{matchId} {action} {serverIp}"
 *   encrypted = AES-CTR-256(plaintext, config_authkey)
 *   payload   = JSON([encrypted, serverIp])
 *   socket.emit("matchCommandSend", payload)
 *
 * This module handles the browser-side Socket.IO connection and status updates.
 * Server-side lifecycle commands (start/stop) are handled by PHP controllers.
 */

/**
 * Initialise the Socket.IO connection to the eBot server.
 * @param {function} callback  Called with the connected socket, or null on failure.
 */
export function initEbotSocket(callback) {
    const config = window.ebotConfig;

    if (! config || ! config.websocketUrl) {
        console.warn('[eBot] No websocket URL configured.');
        callback(null);
        return;
    }

    if (typeof io === 'undefined') {
        console.warn('[eBot] Socket.IO client not loaded.');
        callback(null);
        return;
    }

    try {
        const socket = io(config.websocketUrl, {
            auth: { token: config.jwtToken },
            transports: ['websocket'],
            reconnectionAttempts: 5,
            timeout: 10000,
        });

        socket.on('connect', () => {
            updateConnectionStatus(true);
            console.log('[eBot] Connected to Socket.IO server.');
        });

        socket.on('disconnect', () => {
            updateConnectionStatus(false);
            console.log('[eBot] Disconnected from Socket.IO server.');
        });

        socket.on('connect_error', (err) => {
            updateConnectionStatus(false);
            console.warn('[eBot] Connection error:', err.message);
        });

        callback(socket);
    } catch (e) {
        console.error('[eBot] Failed to initialise socket:', e);
        callback(null);
    }
}

/**
 * Update the WebSocket indicator in the admin navigation bar.
 */
function updateConnectionStatus(connected) {
    const indicator = document.getElementById('ws-indicator');
    const statusText = document.getElementById('ws-status-text');

    if (indicator) {
        indicator.className = connected
            ? 'inline-block h-2 w-2 rounded-full bg-green-400'
            : 'inline-block h-2 w-2 rounded-full bg-gray-500';
    }

    if (statusText) {
        statusText.textContent = connected ? 'Connected' : 'Disconnected';
    }
}

/**
 * Subscribe to real-time match updates (score, status, button refreshes).
 * @param {object} socket  The connected Socket.IO socket.
 * @param {string} room    The Socket.IO room to identify as (e.g. 'matchs', 'match-123').
 */
export function subscribeMatchUpdates(socket, room) {
    if (! socket) return;

    socket.emit('identify', { type: room });

    socket.on('matchsHandler', (rawData) => {
        try {
            const data = typeof rawData === 'string' ? JSON.parse(rawData) : rawData;
            handleMatchEvent(data);
        } catch (e) {
            console.error('[eBot] Failed to parse matchsHandler data:', e);
        }
    });
}

/**
 * Handle a real-time match event from the eBot server.
 */
function handleMatchEvent(data) {
    if (! data) return;

    const matchId = data.id;

    if (data.content === 'stop') {
        // Match ended — reload to show final state
        window.location.reload();
        return;
    }

    if (data.message === 'status' && data.status !== undefined) {
        // Update score display if present
        const scoreEl = document.getElementById(`score-${matchId}`);
        if (scoreEl && data.score_a !== undefined && data.score_b !== undefined) {
            scoreEl.textContent = `${data.score_a} – ${data.score_b}`;
        }
    }

    if (data.message === 'button') {
        // Hide loading indicator when server acknowledges
        const loading = document.getElementById(`loading-${matchId}`);
        if (loading) loading.classList.add('hidden');
    }
}
