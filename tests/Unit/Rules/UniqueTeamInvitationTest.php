<?php

declare(strict_types=1);

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Rules\UniqueTeamInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows unique email not member and no pending invitation', function (): void {
    $team = Team::factory()->create();
    $rule = new UniqueTeamInvitation($team);

    $failed = false;

    $rule->validate('email', 'new@example.com', function (string $message) use (&$failed): void {
        $failed = true;
    });

    expect($failed)->toBeFalse();
});

it('rejects email that is already a team member', function (): void {
    $user = User::factory()->create(['email' => 'member@example.com']);
    $team = Team::factory()->create();
    $team->memberships()->create(['user_id' => $user->id, 'role' => TeamRole::Owner]);

    $rule = new UniqueTeamInvitation($team);

    $failed = false;
    $failMessage = '';

    $rule->validate('email', 'member@example.com', function (string $message) use (&$failed, &$failMessage): void {
        $failed = true;
        $failMessage = $message;
    });

    expect($failed)->toBeTrue()
        ->and($failMessage)->toBe('This user is already a member of the team.');
});

it('rejects email with pending invitation', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $team->memberships()->create(['user_id' => $user->id, 'role' => TeamRole::Owner]);

    TeamInvitation::factory()->create([
        'team_id' => $team->id,
        'email' => 'invited@example.com',
        'role' => TeamRole::Member,
        'invited_by' => $user->id,
        'accepted_at' => null,
    ]);

    $rule = new UniqueTeamInvitation($team);

    $failed = false;
    $failMessage = '';

    $rule->validate('email', 'invited@example.com', function (string $message) use (&$failed, &$failMessage): void {
        $failed = true;
        $failMessage = $message;
    });

    expect($failed)->toBeTrue()
        ->and($failMessage)->toBe('An invitation has already been sent to this email address.');
});

it('is case insensitive', function (): void {
    $user = User::factory()->create(['email' => 'Member@Example.com']);
    $team = Team::factory()->create();
    $team->memberships()->create(['user_id' => $user->id, 'role' => TeamRole::Owner]);

    $rule = new UniqueTeamInvitation($team);

    $failed = false;

    $rule->validate('email', 'member@example.com', function (string $message) use (&$failed): void {
        $failed = true;
    });

    expect($failed)->toBeTrue();
});
