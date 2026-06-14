<?php

declare(strict_types=1);

use App\Http\Middleware\SetTeamUrlDefaults;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

it('sets url defaults when user has current team', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $user->switchTeam($team);

    $middleware = new SetTeamUrlDefaults;

    $request = Request::create('/', 'GET');
    $request->setUserResolver(fn () => $user);

    $response = $middleware->handle($request, fn ($req): Response => response('OK'));

    expect($response->getContent())->toBe('OK')
        ->and($request->route('current_team'))->toBeNull();
});

it('does not throw for guest users', function (): void {
    $middleware = new SetTeamUrlDefaults;

    $request = Request::create('/', 'GET');

    $response = $middleware->handle($request, fn ($req): Response => response('OK'));

    expect($response->getContent())->toBe('OK');
});
