<?php

declare(strict_types=1);

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\TeamInvitation as TeamInvitationModel;
use App\Models\User;
use App\Notifications\Teams\TeamInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('sends via mail channel', function (): void {
    $invitation = TeamInvitationModel::factory()->create();
    $notification = new TeamInvitation($invitation);

    expect($notification->via(new stdClass))->toBe(['mail']);
});

it('builds mail message with correct content', function (): void {
    $user = User::factory()->create(['name' => 'Inviter Name']);
    $team = Team::factory()->create(['name' => 'Test Team']);
    $team->memberships()->create(['user_id' => $user->id, 'role' => TeamRole::Owner]);

    $invitation = TeamInvitationModel::factory()->create([
        'team_id' => $team->id,
        'email' => 'invited@example.com',
        'role' => TeamRole::Member,
        'invited_by' => $user->id,
    ]);

    $notification = new TeamInvitation($invitation);
    $mail = $notification->toMail(new stdClass);

    expect($mail->subject)->toContain('Test Team')
        ->and($mail->actionUrl)->toContain('/invitations/'.$invitation->code.'/accept');
});

it('returns array representation', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['name' => 'Test Team']);
    $team->memberships()->create(['user_id' => $user->id, 'role' => TeamRole::Owner]);

    $invitation = TeamInvitationModel::factory()->create([
        'team_id' => $team->id,
        'email' => 'invited@example.com',
        'role' => TeamRole::Member,
        'invited_by' => $user->id,
    ]);

    $notification = new TeamInvitation($invitation);
    $array = $notification->toArray(new stdClass);

    expect($array)->toHaveKeys(['invitation_id', 'team_id', 'team_name', 'role'])
        ->and($array['team_name'])->toBe('Test Team')
        ->and($array['role'])->toBe('member');
});
