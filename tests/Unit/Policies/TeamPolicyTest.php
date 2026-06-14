<?php

declare(strict_types=1);

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use App\Policies\TeamPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->policy = new TeamPolicy;

    $this->owner = User::factory()->create();
    $this->admin = User::factory()->create();
    $this->member = User::factory()->create();
    $this->team = Team::factory()->create();

    $this->team->memberships()->create(['user_id' => $this->owner->id, 'role' => TeamRole::Owner]);
    $this->team->memberships()->create(['user_id' => $this->admin->id, 'role' => TeamRole::Admin]);
    $this->team->memberships()->create(['user_id' => $this->member->id, 'role' => TeamRole::Member]);
});

it('allows everyone to view any team', function (): void {
    expect($this->policy->viewAny())->toBeTrue();
});

it('allows team members to view their team', function (): void {
    expect($this->policy->view($this->member, $this->team))->toBeTrue();
});

it('denies non-members from viewing a team', function (): void {
    $outsider = User::factory()->create();

    expect($this->policy->view($outsider, $this->team))->toBeFalse();
});

it('allows everyone to create teams', function (): void {
    expect($this->policy->create())->toBeTrue();
});

it('allows owners to update a team', function (): void {
    expect($this->policy->update($this->owner, $this->team))->toBeTrue();
});

it('allows admins to update a team', function (): void {
    expect($this->policy->update($this->admin, $this->team))->toBeTrue();
});

it('denies members from updating a team', function (): void {
    expect($this->policy->update($this->member, $this->team))->toBeFalse();
});

it('allows owners to delete non-personal teams', function (): void {
    expect($this->policy->delete($this->owner, $this->team))->toBeTrue();
});

it('denies deleting personal teams', function (): void {
    $personalTeam = Team::factory()->create(['is_personal' => true]);
    $personalTeam->memberships()->create(['user_id' => $this->owner->id, 'role' => TeamRole::Owner]);

    expect($this->policy->delete($this->owner, $personalTeam))->toBeFalse();
});

it('denies members from deleting teams', function (): void {
    expect($this->policy->delete($this->member, $this->team))->toBeFalse();
});

it('allows owners to add members', function (): void {
    expect($this->policy->addMember($this->owner, $this->team))->toBeTrue();
});

it('denies members from adding members', function (): void {
    expect($this->policy->addMember($this->member, $this->team))->toBeFalse();
});

it('allows owners to remove members', function (): void {
    expect($this->policy->removeMember($this->owner, $this->team))->toBeTrue();
});

it('denies members from removing members', function (): void {
    expect($this->policy->removeMember($this->member, $this->team))->toBeFalse();
});

it('allows owners to invite members', function (): void {
    expect($this->policy->inviteMember($this->owner, $this->team))->toBeTrue();
});

it('allows admins to invite members', function (): void {
    expect($this->policy->inviteMember($this->admin, $this->team))->toBeTrue();
});

it('denies members from inviting members', function (): void {
    expect($this->policy->inviteMember($this->member, $this->team))->toBeFalse();
});

it('allows owners to cancel invitations', function (): void {
    expect($this->policy->cancelInvitation($this->owner, $this->team))->toBeTrue();
});

it('denies members from cancelling invitations', function (): void {
    expect($this->policy->cancelInvitation($this->member, $this->team))->toBeFalse();
});
