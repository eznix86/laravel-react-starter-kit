<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('generates slug on creation', function (): void {
    $team = Team::query()->create(['name' => 'Test Team']);

    expect($team->slug)->toBe('test-team');
});

it('auto-suffixes with existing non-standard slugs', function (): void {
    Team::query()->create(['name' => 'custom', 'slug' => 'custom']);
    Team::query()->create(['name' => 'custom', 'slug' => 'custom-abc']);

    $team = Team::query()->create(['name' => 'custom']);

    expect($team->slug)->toBe('custom-1');
});

it('handles multiple existing suffixes', function (): void {
    Team::query()->create(['name' => 'multi', 'slug' => 'multi']);
    Team::query()->create(['name' => 'multi', 'slug' => 'multi-1']);

    $team = Team::query()->create(['name' => 'multi']);

    expect($team->slug)->toBe('multi-2');
});
