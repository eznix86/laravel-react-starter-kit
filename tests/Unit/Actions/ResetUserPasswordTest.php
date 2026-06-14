<?php

declare(strict_types=1);

use App\Actions\Fortify\ResetUserPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('resets the user password', function (): void {
    $user = User::factory()->create();
    $oldPassword = $user->password;

    $action = resolve(ResetUserPassword::class);
    $action->reset($user, [
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    expect($user->fresh()->password)->not->toBe($oldPassword);
});

it('validates password rules', function (): void {
    $user = User::factory()->create();
    $action = resolve(ResetUserPassword::class);

    expect(fn () => $action->reset($user, []))
        ->toThrow(ValidationException::class);
});
