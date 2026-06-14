<?php

declare(strict_types=1);

use App\Http\Responses\VerifyEmailResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;

it('redirects to intended url with team prefix', function (): void {
    Fortify::redirects('email-verification', '/dashboard');

    $request = Request::create('/email/verify', 'GET');
    $request->setUserResolver(fn () => User::factory()->create());

    $response = (new VerifyEmailResponse)->toResponse($request);

    expect($response->getStatusCode())->toBe(302);
});

it('returns json response when wants json', function (): void {
    $request = Request::create('/email/verify', 'GET');
    $request->headers->set('Accept', 'application/json');
    $request->setUserResolver(fn () => User::factory()->create());

    $response = (new VerifyEmailResponse)->toResponse($request);

    expect($response->getStatusCode())->toBe(204);
});
