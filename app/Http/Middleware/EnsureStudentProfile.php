<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentProfile
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless($user?->is_active && $user->siswa && $user->siswa->status === 'Aktif', 403,
            'Profil siswa belum aktif. Silakan hubungi administrasi sekolah.');

        return $next($request);
    }
}
