<?php

use App\Services\ToornamentService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

describe('ToornamentService', function () {
    function makeToornamentService(): ToornamentService
    {
        return new ToornamentService('client-id', 'client-secret', 'api-key');
    }

    it('reports not configured when credentials are empty', function () {
        $service = new ToornamentService('', '', '');
        expect($service->isConfigured())->toBeFalse();
    });

    it('reports configured when credentials are present', function () {
        $service = makeToornamentService();
        expect($service->isConfigured())->toBeTrue();
    });

    it('fetches an OAuth2 token and caches it', function () {
        Cache::flush();

        Http::fake([
            '*oauth*'        => Http::response(json_encode(['access_token' => 'cached-token']), 200),
            '*v1/tournaments' => Http::response(json_encode([]), 200),
        ]);

        $service = makeToornamentService();
        $service->getTournaments();

        expect(Cache::has('toornament_access_token'))->toBeTrue();
        expect(Cache::get('toornament_access_token'))->toBe('cached-token');
    });

    it('uses cached token without re-requesting OAuth2', function () {
        Cache::put('toornament_access_token', 'pre-cached', 3600);

        Http::fake([
            '*v1/tournaments' => Http::response(json_encode([['id' => 't1']]), 200),
        ]);

        $service = makeToornamentService();
        $result  = $service->getTournaments();

        expect($result)->toBe([['id' => 't1']]);

        Http::assertNotSent(fn ($r) => str_contains($r->url(), 'oauth'));
    });

    it('fetches tournament matches', function () {
        Cache::put('toornament_access_token', 'tok', 3600);

        Http::fake([
            '*tournaments/t1/matches*' => Http::response(json_encode([
                ['id' => 'm1'],
                ['id' => 'm2'],
            ]), 200),
        ]);

        $service  = makeToornamentService();
        $matches  = $service->getTournamentMatches('t1');

        expect($matches)->toHaveCount(2);
        expect($matches[0]['id'])->toBe('m1');
    });

    it('throws on API error response', function () {
        Cache::put('toornament_access_token', 'tok', 3600);

        Http::fake([
            '*v1/tournaments' => Http::response('Unauthorized', 401),
        ]);

        $service = makeToornamentService();
        expect(fn () => $service->getTournaments())->toThrow(\RuntimeException::class);
    });

    it('throws when OAuth2 token request fails', function () {
        Cache::flush();

        Http::fake([
            '*oauth*' => Http::response('Bad credentials', 401),
        ]);

        $service = makeToornamentService();
        expect(fn () => $service->getTournaments())->toThrow(\RuntimeException::class);
    });
});
