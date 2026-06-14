<?php

declare(strict_types=1);

use App\Concerns\PasswordValidationRules;

it('returns password rules', function (): void {
    $class = new class
    {
        use PasswordValidationRules;
    };
    $ref = new ReflectionMethod($class, 'passwordRules');

    $rules = $ref->invoke($class);

    expect($rules)->toBeArray()
        ->and($rules)->toContain('required', 'confirmed');
});

it('returns current password rules', function (): void {
    $class = new class
    {
        use PasswordValidationRules;
    };
    $ref = new ReflectionMethod($class, 'currentPasswordRules');

    $rules = $ref->invoke($class);

    expect($rules)->toBeArray()
        ->and($rules)->toContain('required', 'current_password');
});
