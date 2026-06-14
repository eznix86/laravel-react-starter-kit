<?php

declare(strict_types=1);

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('generates a slug on creation', function (): void {
    $team = Team::query()->create(['name' => 'My Awesome Team']);

    expect($team->slug)->toBe('my-awesome-team');
});

it('auto-suffixes duplicate slugs', function (): void {
    Team::query()->create(['name' => 'duplicate', 'slug' => 'duplicate']);
    $team = Team::query()->create(['name' => 'duplicate']);

    expect($team->slug)->toStartWith('duplicate')
        ->and($team->slug)->not->toBe('duplicate');
});

it('regenerates slug when name changes', function (): void {
    $team = Team::query()->create(['name' => 'Original Name']);

    $team->update(['name' => 'New Name']);

    expect($team->fresh()->slug)->toBe('new-name');
});

it('uses slug as route key', function (): void {
    $team = Team::query()->create(['name' => 'Test']);

    expect($team->getRouteKeyName())->toBe('slug')
        ->and($team->getRouteKey())->toBe($team->slug);
});

it('identifies the owner', function (): void {
    $user = User::factory()->create();
    $team = Team::query()->create(['name' => 'Test']);
    $team->memberships()->create(['user_id' => $user->id, 'role' => TeamRole::Owner]);
    $user->switchTeam($team);

    expect($team->fresh()->owner()->id)->toBe($user->id);
});

it('has members relationship', function (): void {
    $user = User::factory()->create();
    $team = Team::query()->create(['name' => 'Test']);
    $team->memberships()->create(['user_id' => $user->id, 'role' => TeamRole::Owner]);

    expect($team->fresh()->members->count())->toBe(1)
        ->and($team->members->first()->id)->toBe($user->id);
});

it('has memberships relationship', function (): void {
    $user = User::factory()->create();
    $team = Team::query()->create(['name' => 'Test']);
    $team->memberships()->create(['user_id' => $user->id, 'role' => TeamRole::Owner]);

    expect($team->fresh()->memberships->count())->toBe(1)
        ->and($team->memberships->first()->role)->toBe(TeamRole::Owner);
});

it('has invitations relationship', function (): void {
    $user = User::factory()->create();
    $team = Team::query()->create(['name' => 'Test']);

    $team->invitations()->create([
        'email' => 'invite@example.com',
        'role' => TeamRole::Member,
        'invited_by' => $user->id,
    ]);

    expect($team->fresh()->invitations->count())->toBe(1);
});
