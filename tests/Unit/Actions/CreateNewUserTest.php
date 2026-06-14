<?php

declare(strict_types=1);

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('creates a user and a personal team', function (): void {
    $action = resolve(CreateNewUser::class);

    $user = $action->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('Test User')
        ->and($user->email)->toBe('test@example.com')
        ->and($user->password)->not->toBe('password')
        ->and($user->currentTeam)->not->toBeNull()
        ->and($user->currentTeam->is_personal)->toBeTrue()
        ->and($user->currentTeam->name)->toBe("Test User's Team");
});

it('validates required fields', function (): void {
    $action = resolve(CreateNewUser::class);

    expect(fn () => $action->create([]))->toThrow(ValidationException::class);
});

it('validates unique email', function (): void {
    User::factory()->create(['email' => 'existing@example.com']);

    $action = resolve(CreateNewUser::class);

    expect(fn () => $action->create([
        'name' => 'Test User',
        'email' => 'existing@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]))->toThrow(ValidationException::class);
});
