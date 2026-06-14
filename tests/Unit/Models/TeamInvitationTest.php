<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to team', function (): void {
    $team = Team::factory()->create();
    $user = User::factory()->create();
    $invitation = TeamInvitation::factory()->create([
        'team_id' => $team->id,
        'invited_by' => $user->id,
    ]);

    expect($invitation->team->id)->toBe($team->id);
});

it('belongs to inviter', function (): void {
    $user = User::factory()->create();
    $invitation = TeamInvitation::factory()->create([
        'invited_by' => $user->id,
    ]);

    expect($invitation->inviter->id)->toBe($user->id);
});

it('detects accepted invitation', function (): void {
    $invitation = TeamInvitation::factory()->create(['accepted_at' => now()]);

    expect($invitation->isAccepted())->toBeTrue();
});

it('detects pending invitation', function (): void {
    $invitation = TeamInvitation::factory()->create([
        'accepted_at' => null,
        'expires_at' => now()->addDay(),
    ]);

    expect($invitation->isPending())->toBeTrue();
});

it('detects expired invitation', function (): void {
    $invitation = TeamInvitation::factory()->create([
        'accepted_at' => null,
        'expires_at' => now()->subDay(),
    ]);

    expect($invitation->isExpired())->toBeTrue();
});

it('uses code as route key', function (): void {
    $invitation = TeamInvitation::factory()->create();

    expect($invitation->getRouteKeyName())->toBe('code')
        ->and($invitation->getRouteKey())->toBe($invitation->code);
});
