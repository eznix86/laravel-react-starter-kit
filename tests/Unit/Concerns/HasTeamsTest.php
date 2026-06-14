<?php

declare(strict_types=1);

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns owned teams for user', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $team->memberships()->create(['user_id' => $user->id, 'role' => TeamRole::Owner]);

    expect($user->fresh()->ownedTeams->count())->toBe(2);
});

it('excludes non-owned teams', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $team->memberships()->create(['user_id' => $user->id, 'role' => TeamRole::Member]);

    expect($user->fresh()->ownedTeams->count())->toBe(1); // only personal team
});

it('checks if user owns team', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $team->memberships()->create(['user_id' => $user->id, 'role' => TeamRole::Owner]);

    expect($user->fresh()->ownsTeam($team))->toBeTrue();
});

it('checks if user does not own team', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $team->memberships()->create(['user_id' => $user->id, 'role' => TeamRole::Member]);

    expect($user->fresh()->ownsTeam($team))->toBeFalse();
});
