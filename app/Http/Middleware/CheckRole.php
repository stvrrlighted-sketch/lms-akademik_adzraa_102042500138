<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$peranDiizinkan): Response
    {
        // Autentikasi harus lolos dulu; tanpa ini $request->user() null dan otorisasi tidak bermakna.
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Pakai abort 403, bukan redirect: pengguna yang sudah login tidak boleh berputar di
        // halaman login, dan penolakan otorisasi harus jelas terpisah dari kegagalan autentikasi.
        if (! in_array($request->user()->role, $peranDiizinkan, true)) {
            abort(403, 'Anda tidak memiliki hak akses!');
        }

        return $next($request);
    }
}
