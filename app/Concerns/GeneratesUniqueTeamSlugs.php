<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Team;
use Illuminate\Database\Eloquent\Attributes\Boot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait GeneratesUniqueTeamSlugs
{
    #[Boot]
    protected static function generateSlugs(): void
    {
        static::creating(function (Model $model): void {
            /** @var Team $model */
            if (empty($model->slug)) {
                $model->slug = static::generateUniqueTeamSlug($model->name);
            }
        });

        static::updating(function (Model $model): void {
            /** @var Team $model */
            if ($model->isDirty('name')) {
                $model->slug = static::generateUniqueTeamSlug($model->name, $model->id);
            }
        });
    }

    /**
     * Generate a unique slug for the team.
     */
    protected static function generateUniqueTeamSlug(string $name, ?int $excludeId = null): string
    {
        $defaultSlug = Str::slug($name);

        $query = static::withTrashed()
            ->where(function ($query) use ($defaultSlug): void {
                $query->where('slug', $defaultSlug)
                    ->orWhere('slug', 'like', $defaultSlug.'-%');
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $existingSlugs = $query->pluck('slug');

        $maxSuffix = $existingSlugs
            ->map(function (string $slug) use ($defaultSlug): ?int {
                if ($slug === $defaultSlug) {
                    return 0;
                }

                if (preg_match('/^'.preg_quote($defaultSlug, '/').'-(\d+)$/', $slug, $matches)) {
                    return (int) $matches[1];
                }

                return null;
            })
            ->filter(fn (?int $suffix): bool => $suffix !== null)
            ->max() ?? 0;

        return $existingSlugs->isEmpty()
            ? $defaultSlug
            : $defaultSlug.'-'.($maxSuffix + 1);
    }
}
