<?php

declare(strict_types=1);

use App\Data\TeamPermissions;

it('constructs with all permissions', function (): void {
    $permissions = new TeamPermissions(
        canUpdateTeam: true,
        canDeleteTeam: false,
        canAddMember: true,
        canUpdateMember: false,
        canRemoveMember: true,
        canCreateInvitation: false,
        canCancelInvitation: true,
    );

    expect($permissions->canUpdateTeam)->toBeTrue()
        ->and($permissions->canDeleteTeam)->toBeFalse()
        ->and($permissions->canAddMember)->toBeTrue()
        ->and($permissions->canUpdateMember)->toBeFalse()
        ->and($permissions->canRemoveMember)->toBeTrue()
        ->and($permissions->canCreateInvitation)->toBeFalse()
        ->and($permissions->canCancelInvitation)->toBeTrue();
});
