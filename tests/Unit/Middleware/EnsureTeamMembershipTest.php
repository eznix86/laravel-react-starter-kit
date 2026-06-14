<?php

declare(strict_types=1);

use App\Enums\TeamRole;
use App\Http\Middleware\EnsureTeamMembership;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

function routeWithParam(string $param, mixed $value): Route
{
    return tap(new Route('GET', '/', fn (): string => 'OK'), function (Route $route) use ($param, $value): void {
        $route->parameters = [$param => $value];
    });
}

it('allows team members to proceed', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $team->memberships()->create(['user_id' => $user->id, 'role' => TeamRole::Owner]);
    $user->switchTeam($team);
    $user->refresh();

    $middleware = new EnsureTeamMembership;

    $request = Request::create('/', 'GET');
    $request->setUserResolver(fn () => $user);
    $request->setRouteResolver(fn (): Route => routeWithParam('current_team', $team->slug));

    $response = $middleware->handle($request, fn ($req): Response => response('OK'));

    expect($response->getContent())->toBe('OK');
});

it('blocks non-members with 403', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();

    $middleware = new EnsureTeamMembership;

    $request = Request::create('/', 'GET');
    $request->setUserResolver(fn () => $user);
    $request->setRouteResolver(fn (): Route => routeWithParam('current_team', $team->slug));

    expect(fn (): Symfony\Component\HttpFoundation\Response => $middleware->handle($request, fn ($req): Response => response('OK')))
        ->toThrow(HttpException::class);
});

it('blocks members without sufficient role', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $team->memberships()->create(['user_id' => $user->id, 'role' => TeamRole::Member]);
    $user->switchTeam($team);
    $user->refresh();

    $middleware = new EnsureTeamMembership;

    $request = Request::create('/', 'GET');
    $request->setUserResolver(fn () => $user);
    $request->setRouteResolver(fn (): Route => routeWithParam('current_team', $team->slug));

    expect(fn (): Symfony\Component\HttpFoundation\Response => $middleware->handle($request, fn ($req): Response => response('OK'), 'admin'))
        ->toThrow(HttpException::class);
});

it('allows owners through any role gate', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $team->memberships()->create(['user_id' => $user->id, 'role' => TeamRole::Owner]);
    $user->switchTeam($team);
    $user->refresh();

    $middleware = new EnsureTeamMembership;

    $request = Request::create('/', 'GET');
    $request->setUserResolver(fn () => $user);
    $request->setRouteResolver(fn (): Route => routeWithParam('current_team', $team->slug));

    $response = $middleware->handle($request, fn ($req): Response => response('OK'), 'admin');

    expect($response->getContent())->toBe('OK');
});

it('switches team when current_team param differs', function (): void {
    $user = User::factory()->create();
    $firstTeam = Team::factory()->create();
    $secondTeam = Team::factory()->create();
    $firstTeam->memberships()->create(['user_id' => $user->id, 'role' => TeamRole::Owner]);
    $secondTeam->memberships()->create(['user_id' => $user->id, 'role' => TeamRole::Member]);
    $user->switchTeam($firstTeam);
    $user->refresh();

    $middleware = new EnsureTeamMembership;

    $request = Request::create('/', 'GET');
    $request->setUserResolver(fn () => $user);
    $request->setRouteResolver(fn (): Route => routeWithParam('current_team', $secondTeam->slug));

    $middleware->handle($request, fn ($req): Response => response('OK'));

    expect($user->fresh()->current_team_id)->toBe($secondTeam->id);
});
