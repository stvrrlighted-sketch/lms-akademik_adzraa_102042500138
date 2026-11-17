<?php

namespace App\Http\Middleware;

use App\Services\PenerjemahHalaman;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response as ResponsHttp;
use Symfony\Component\HttpFoundation\Response;

class TerjemahkanHalaman
{
    public function handle(Request $request, Closure $next): Response
    {
        $respons = $next($request);

        if (app()->getLocale() !== 'en' || ! $respons instanceof ResponsHttp || $respons->isRedirection()) {
            return $respons;
        }

        $jenis = (string) $respons->headers->get('Content-Type', 'text/html');
        $isi = $respons->getContent();

        if (! str_contains($jenis, 'text/html') || ! is_string($isi) || $isi === '') {
            return $respons;
        }

        $respons->setContent(app(PenerjemahHalaman::class)->terjemahkan($isi));

        return $respons;
    }
}
