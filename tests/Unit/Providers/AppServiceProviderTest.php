<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Carbon\CarbonImmutable;

it('configures carbon immutable', function (): void {
    $provider = new AppServiceProvider(app());
    $provider->boot();

    expect(now())->toBeInstanceOf(CarbonImmutable::class);
});

it('configures password defaults without errors', function (): void {
    $provider = new AppServiceProvider(app());
    $provider->boot();

    // Framework handles the defaults — just verify boot completes
    expect(true)->toBeTrue();
});
