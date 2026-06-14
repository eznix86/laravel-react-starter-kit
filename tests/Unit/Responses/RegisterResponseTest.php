<?php

declare(strict_types=1);

use App\Http\Responses\RegisterResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;

it('redirects to intended url with team prefix', function (): void {
    Fortify::redirects('register', '/dashboard');

    $request = Request::create('/register', 'POST');
    $request->setUserResolver(fn () => User::factory()->create());

    $response = (new RegisterResponse)->toResponse($request);

    expect($response->getStatusCode())->toBe(302);
});

it('returns json response when wants json', function (): void {
    $request = Request::create('/register', 'POST');
    $request->headers->set('Accept', 'application/json');
    $request->setUserResolver(fn () => User::factory()->create());

    $response = (new RegisterResponse)->toResponse($request);

    expect($response->getStatusCode())->toBe(201)
        ->and(json_decode($response->getContent(), true))->toBe(['two_factor' => false]);
});
