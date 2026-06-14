<?php

declare(strict_types=1);

use App\Data\UserTeam;

it('constructs with all properties', function (): void {
    $team = new UserTeam(
        id: 1,
        name: 'My Team',
        slug: 'my-team',
        isPersonal: false,
        role: 'owner',
        roleLabel: 'Owner',
        isCurrent: true,
    );

    expect($team->id)->toBe(1)
        ->and($team->name)->toBe('My Team')
        ->and($team->slug)->toBe('my-team')
        ->and($team->isPersonal)->toBeFalse()
        ->and($team->role)->toBe('owner')
        ->and($team->roleLabel)->toBe('Owner')
        ->and($team->isCurrent)->toBeTrue();
});

it('defaults isCurrent to null', function (): void {
    $team = new UserTeam(
        id: 2,
        name: 'Other',
        slug: 'other',
        isPersonal: true,
        role: null,
        roleLabel: null,
    );

    expect($team->isCurrent)->toBeNull();
});
