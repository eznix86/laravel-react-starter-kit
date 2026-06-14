<?php

declare(strict_types=1);
use App\Models\TeamInvitation;
use App\Models\User;
use App\Rules\ValidTeamInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('accepts a valid pending invitation', function (): void {
    $user = User::factory()->create(['email' => 'invited@example.com']);

    $invitation = TeamInvitation::factory()->create([
        'email' => 'invited@example.com',
        'accepted_at' => null,
        'expires_at' => now()->addDay(),
    ]);

    $rule = new ValidTeamInvitation($user);

    $failed = false;

    $rule->validate('code', $invitation, function (string $message) use (&$failed): void {
        $failed = true;
    });

    expect($failed)->toBeFalse();
});

it('rejects already accepted invitation', function (): void {
    $user = User::factory()->create(['email' => 'invited@example.com']);

    $invitation = TeamInvitation::factory()->create([
        'email' => 'invited@example.com',
        'accepted_at' => now(),
    ]);

    $rule = new ValidTeamInvitation($user);

    $failed = false;
    $failMessage = '';

    $rule->validate('code', $invitation, function (string $message) use (&$failed, &$failMessage): void {
        $failed = true;
        $failMessage = $message;
    });

    expect($failed)->toBeTrue()
        ->and($failMessage)->toBe('This invitation has already been accepted.');
});

it('rejects expired invitation', function (): void {
    $user = User::factory()->create(['email' => 'invited@example.com']);

    $invitation = TeamInvitation::factory()->create([
        'email' => 'invited@example.com',
        'accepted_at' => null,
        'expires_at' => now()->subDay(),
    ]);

    $rule = new ValidTeamInvitation($user);

    $failed = false;
    $failMessage = '';

    $rule->validate('code', $invitation, function (string $message) use (&$failed, &$failMessage): void {
        $failed = true;
        $failMessage = $message;
    });

    expect($failed)->toBeTrue()
        ->and($failMessage)->toBe('This invitation has expired.');
});

it('rejects invitation with different email', function (): void {
    $user = User::factory()->create(['email' => 'user@example.com']);

    $invitation = TeamInvitation::factory()->create([
        'email' => 'other@example.com',
        'accepted_at' => null,
    ]);

    $rule = new ValidTeamInvitation($user);

    $failed = false;
    $failMessage = '';

    $rule->validate('code', $invitation, function (string $message) use (&$failed, &$failMessage): void {
        $failed = true;
        $failMessage = $message;
    });

    expect($failed)->toBeTrue()
        ->and($failMessage)->toBe('This invitation was sent to a different email address.');
});

it('rejects invitation for guest user', function (): void {
    $invitation = TeamInvitation::factory()->create([
        'email' => 'invited@example.com',
        'accepted_at' => null,
    ]);

    $rule = new ValidTeamInvitation(null);

    $failed = false;

    $rule->validate('code', $invitation, function (string $message) use (&$failed): void {
        $failed = true;
    });

    expect($failed)->toBeTrue();
});

it('is case insensitive for email comparison', function (): void {
    $user = User::factory()->create(['email' => 'Invited@Example.com']);

    $invitation = TeamInvitation::factory()->create([
        'email' => 'INVITED@EXAMPLE.COM',
        'accepted_at' => null,
    ]);

    $rule = new ValidTeamInvitation($user);

    $failed = false;

    $rule->validate('code', $invitation, function (string $message) use (&$failed): void {
        $failed = true;
    });

    expect($failed)->toBeFalse();
});
