<?php

declare(strict_types=1);

use App\Actions\Teams\CreateTeam;
use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a team with the user as owner', function (): void {
    $user = User::factory()->create();
    $action = resolve(CreateTeam::class);

    $team = $action->handle($user, 'My Team');

    expect($team)->toBeInstanceOf(Team::class)
        ->and($team->name)->toBe('My Team')
        ->and($team->is_personal)->toBeFalse()
        ->and($team->slug)->not->toBeNull()
        ->and($user->teamRole($team))->toBe(TeamRole::Owner)
        ->and($user->isCurrentTeam($team))->toBeTrue();
});

it('creates a personal team', function (): void {
    $user = User::factory()->create();
    $action = resolve(CreateTeam::class);

    $team = $action->handle($user, 'Personal', isPersonal: true);

    expect($team->is_personal)->toBeTrue();
});

it('auto-suffixes duplicate team slugs', function (): void {
    $user = User::factory()->create();
    $action = resolve(CreateTeam::class);

    $first = $action->handle($user, 'Duplicate');
    $second = $action->handle($user, 'Duplicate');

    expect($first->slug)->not->toBe($second->slug)
        ->and($second->slug)->toStartWith('duplicate');
});
