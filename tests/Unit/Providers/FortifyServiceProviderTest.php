<?php

declare(strict_types=1);

use App\Providers\FortifyServiceProvider;
use Illuminate\Support\Facades\RateLimiter;

it('registers all rate limiters', function (): void {
    $provider = new FortifyServiceProvider(app());
    $provider->boot();

    expect(RateLimiter::limiter('login'))->not->toBeNull()
        ->and(RateLimiter::limiter('two-factor'))->not->toBeNull()
        ->and(RateLimiter::limiter('passkeys'))->not->toBeNull();
});

it('configures fortify without errors', function (): void {
    $provider = new FortifyServiceProvider(app());
    $provider->boot();

    // Boot configures actions, views, rate limiters
    expect(true)->toBeTrue();
});
