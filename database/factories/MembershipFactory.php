<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TeamRole;
use App\Models\Membership;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Membership>
 */
final class MembershipFactory extends Factory
{
    protected $model = Membership::class;

    public function definition(): array
    {
        return [
            'role' => TeamRole::Owner,
        ];
    }
}
