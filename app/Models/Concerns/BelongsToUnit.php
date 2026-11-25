<?php

namespace App\Models\Concerns;

use App\Models\Unit;
use App\Models\User;
use App\Support\UnitContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToUnit
{
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function scopeForUnit(Builder $query, ?int $unitId, bool $includeGlobal = false): Builder
    {
        if ($unitId === null) {
            return $query;
        }

        $column = $this->qualifyColumn('unit_id');

        if ($includeGlobal) {
            return $query->where(function (Builder $q) use ($column, $unitId) {
                $q->where($column, $unitId)->orWhereNull($column);
            });
        }

        return $query->where($column, $unitId);
    }

    public function scopeForCurrentUser(Builder $query, bool $includeGlobal = false, ?User $user = null): Builder
    {
        return $this->scopeForUnit($query, UnitContext::activeUnitId($user), $includeGlobal);
    }
}
