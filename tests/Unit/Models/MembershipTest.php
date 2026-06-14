<?php

declare(strict_types=1);

use App\Enums\TeamRole;
use App\Models\Membership;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to team', function (): void {
    $team = Team::factory()->create();
    $user = User::factory()->create();
    $membership = Membership::factory()->create([
        'team_id' => $team->id,
        'user_id' => $user->id,
        'role' => TeamRole::Owner,
    ]);

    expect($membership->team->id)->toBe($team->id);
});

it('belongs to user', function (): void {
    $team = Team::factory()->create();
    $user = User::factory()->create();
    $membership = Membership::factory()->create([
        'team_id' => $team->id,
        'user_id' => $user->id,
        'role' => TeamRole::Owner,
    ]);

    expect($membership->user->id)->toBe($user->id);
});

it('casts role to enum', function (): void {
    $team = Team::factory()->create();
    $user = User::factory()->create();
    $membership = Membership::factory()->create([
        'team_id' => $team->id,
        'user_id' => $user->id,
        'role' => TeamRole::Admin,
    ]);

    expect($membership->role)->toBe(TeamRole::Admin);
});
