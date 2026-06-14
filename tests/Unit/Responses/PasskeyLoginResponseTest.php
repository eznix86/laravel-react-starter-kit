<?php

declare(strict_types=1);

use App\Http\Responses\PasskeyLoginResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;

it('redirects to intended url with team prefix', function (): void {
    Fortify::redirects('login', '/dashboard');

    $request = Request::create('/login', 'POST');
    $request->setUserResolver(fn () => User::factory()->create());

    $response = (new PasskeyLoginResponse)->toResponse($request);

    expect($response->getStatusCode())->toBe(302);
});

it('returns json response when wants json', function (): void {
    Fortify::redirects('login', '/dashboard');

    $request = Request::create('/login', 'POST');
    $request->headers->set('Accept', 'application/json');
    $request->setUserResolver(fn () => User::factory()->create());

    $response = (new PasskeyLoginResponse)->toResponse($request);

    expect($response->getStatusCode())->toBe(200);
});
