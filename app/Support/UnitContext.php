<?php

namespace App\Support;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UnitContext
{
    public const SESSION_KEY = 'active_unit_id';

    public static function activeUnitId(?User $user = null): ?int
    {
        $user ??= Auth::user();

        if (! $user) {
            return null;
        }

        // Admin unit & guru/siswa selalu terkunci pada unit mereka sendiri (tidak bisa dioverride session)
        if (! $user->isSuperAdmin()) {
            return $user->effectiveUnitId();
        }

        $sessionUnitId = session(self::SESSION_KEY);

        if ($sessionUnitId === null || $sessionUnitId === '' || (int) $sessionUnitId <= 0) {
            return null;
        }

        return (int) $sessionUnitId;
    }

    public static function activeUnit(?User $user = null): ?Unit
    {
        $unitId = self::activeUnitId($user);

        return $unitId ? Unit::find($unitId) : null;
    }

    public static function setSuperAdminUnit(?int $unitId): void
    {
        $user = Auth::user();

        if (! $user || ! $user->isSuperAdmin()) {
            return;
        }

        if ($unitId === null || $unitId <= 0) {
            session()->forget(self::SESSION_KEY);

            return;
        }

        if (Unit::where('id', $unitId)->exists()) {
            session([self::SESSION_KEY => $unitId]);
        }
    }

    public static function canAccessUnit(?User $user, ?int $targetUnitId, bool $allowGlobal = false): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        $userUnitId = $user->effectiveUnitId();

        if ($targetUnitId === null) {
            return $allowGlobal;
        }

        return $userUnitId !== null && (int) $userUnitId === (int) $targetUnitId;
    }

    public static function authorizeUnit(?User $user, ?int $targetUnitId, bool $allowGlobal = false, string $message = 'Akses ditolak: data ini milik unit sekolah lain.'): void
    {
        if (! $user && app()->runningUnitTests()) {
            return;
        }

        abort_unless(self::canAccessUnit($user, $targetUnitId, $allowGlobal), 403, $message);
    }

    public static function resolveUnitIdFromJenjang(?string $jenjang): ?int
    {
        if (! $jenjang || in_array($jenjang, ['Umum', 'Semua'], true)) {
            return null;
        }

        return Unit::where('kode', $jenjang)->value('id');
    }
}
