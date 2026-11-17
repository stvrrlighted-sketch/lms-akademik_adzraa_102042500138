<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AturBahasa
{
    public const DAFTAR_BAHASA = ['id', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $kode = $request->session()->get('bahasa') ?? $request->cookie('bahasa');

        // Hanya kode yang ada di daftar yang diterima; nilai lain dari sesi atau cookie diabaikan.
        app()->setLocale(in_array($kode, self::DAFTAR_BAHASA, true) ? $kode : 'id');

        return $next($request);
    }
}
