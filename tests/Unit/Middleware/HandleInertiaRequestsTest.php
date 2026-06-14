<?php

declare(strict_types=1);

use App\Http\Middleware\HandleInertiaRequests;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

beforeEach(function (): void {
    Config::set('app.name', 'Laravel');
});

it('shares app name with all requests', function (): void {
    $middleware = new HandleInertiaRequests;

    $request = Request::create('/', 'GET');

    $shared = $middleware->share($request);

    expect($shared['name'])->toBe('Laravel');
});

it('shares null user for guest requests', function (): void {
    $middleware = new HandleInertiaRequests;

    $request = Request::create('/', 'GET');

    $shared = $middleware->share($request);

    expect($shared['auth']['user'])->toBeNull();
});

it('shares authenticated user data', function (): void {
    $middleware = new HandleInertiaRequests;
    $user = User::factory()->create();

    $request = Request::create('/', 'GET');
    $request->setUserResolver(fn () => $user);

    $shared = $middleware->share($request);

    expect($shared['auth']['user']->id)->toBe($user->id);
});

it('sidebar open defaults to true when no cookie', function (): void {
    $middleware = new HandleInertiaRequests;

    $request = Request::create('/', 'GET');

    $shared = $middleware->share($request);

    expect($shared['sidebarOpen'])->toBeTrue();
});

it('sidebar open is true when cookie is true', function (): void {
    $middleware = new HandleInertiaRequests;

    $request = Request::create('/', 'GET');
    $request->cookies->set('sidebar_state', 'true');

    $shared = $middleware->share($request);

    expect($shared['sidebarOpen'])->toBeTrue();
});

it('sidebar open is false when cookie is false', function (): void {
    $middleware = new HandleInertiaRequests;

    $request = Request::create('/', 'GET');
    $request->cookies->set('sidebar_state', 'false');

    $shared = $middleware->share($request);

    expect($shared['sidebarOpen'])->toBeFalse();
});
