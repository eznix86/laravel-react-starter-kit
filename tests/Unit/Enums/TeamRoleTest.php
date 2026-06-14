<?php

declare(strict_types=1);

use App\Enums\TeamPermission;
use App\Enums\TeamRole;

it('returns correct label for each role', function (TeamRole $role, string $label): void {
    expect($role->label())->toBe($label);
})->with([
    [TeamRole::Owner, 'Owner'],
    [TeamRole::Admin, 'Admin'],
    [TeamRole::Member, 'Member'],
]);

it('returns owner permissions', function (): void {
    expect(TeamRole::Owner->permissions())->toBe(TeamPermission::cases());
});

it('returns admin permissions', function (): void {
    expect(TeamRole::Admin->permissions())->toBe([
        TeamPermission::UpdateTeam,
        TeamPermission::CreateInvitation,
        TeamPermission::CancelInvitation,
    ]);
});

it('returns empty permissions for member', function (): void {
    expect(TeamRole::Member->permissions())->toBeEmpty();
});

it('checks if role has permission', function (): void {
    expect(TeamRole::Owner->hasPermission(TeamPermission::DeleteTeam))->toBeTrue()
        ->and(TeamRole::Admin->hasPermission(TeamPermission::UpdateTeam))->toBeTrue()
        ->and(TeamRole::Admin->hasPermission(TeamPermission::DeleteTeam))->toBeFalse()
        ->and(TeamRole::Member->hasPermission(TeamPermission::UpdateTeam))->toBeFalse();
});

it('returns correct hierarchy level', function (): void {
    expect(TeamRole::Owner->level())->toBe(3)
        ->and(TeamRole::Admin->level())->toBe(2)
        ->and(TeamRole::Member->level())->toBe(1);
});

it('checks if role is at least another role', function (): void {
    expect(TeamRole::Owner->isAtLeast(TeamRole::Admin))->toBeTrue()
        ->and(TeamRole::Owner->isAtLeast(TeamRole::Member))->toBeTrue()
        ->and(TeamRole::Admin->isAtLeast(TeamRole::Member))->toBeTrue()
        ->and(TeamRole::Admin->isAtLeast(TeamRole::Owner))->toBeFalse()
        ->and(TeamRole::Member->isAtLeast(TeamRole::Admin))->toBeFalse();
});

it('returns assignable roles excluding owner', function (): void {
    $assignable = TeamRole::assignable();

    expect($assignable)->toBe([
        ['value' => 'admin', 'label' => 'Admin'],
        ['value' => 'member', 'label' => 'Member'],
    ]);
});
