<?php

declare(strict_types=1);

use App\Rules\TeamName;

it('allows regular team names', function (string $name): void {
    $rule = new TeamName;

    $failed = false;

    $rule->validate('name', $name, function (string $message) use (&$failed): void {
        $failed = true;
    });

    expect($failed)->toBeFalse();
})->with([
    'My Team',
    'Acme Corp',
    'devs',
    'project-alpha',
]);

it('rejects reserved team names', function (string $name): void {
    $rule = new TeamName;

    $failed = false;

    $rule->validate('name', $name, function (string $message) use (&$failed): void {
        $failed = true;
    });

    expect($failed)->toBeTrue();
})->with([
    'admin',
    'api',
    'dashboard',
    'settings',
    'login',
    'logout',
    'register',
    'teams',
]);

it('rejects http status code names', function (): void {
    $rule = new TeamName;

    $failed = false;

    $rule->validate('name', '404', function (string $message) use (&$failed): void {
        $failed = true;
    });

    expect($failed)->toBeTrue();
});

it('is case insensitive', function (): void {
    $rule = new TeamName;

    $failed = false;

    $rule->validate('name', 'ADMIN', function (string $message) use (&$failed): void {
        $failed = true;
    });

    expect($failed)->toBeTrue();
});
