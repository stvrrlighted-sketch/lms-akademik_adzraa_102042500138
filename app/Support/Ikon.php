<?php

namespace App\Support;

class Ikon
{
    private const ALIAS = [
        'buku' => 'books',
        'grafik' => 'chart-bar',
        'pengguna' => 'users',
        'centang' => 'check-circle',
        'berkas' => 'file-text',
        'perisai' => 'shield-check',
        'gembok' => 'lock-simple',
        'dasbor' => 'squares-four',
        'berita' => 'newspaper',
        'kalender' => 'calendar-blank',
        'topi' => 'graduation-cap',
    ];

    private static array $cache = [];

    public static function svg(string $nama, string $kelas = 'ikon'): string
    {
        $nama = self::ALIAS[$nama] ?? $nama;

        // Nama ikon dipakai untuk membentuk path berkas; hanya huruf kecil, angka, dan tanda hubung yang diizinkan.
        if (! preg_match('/^[a-z0-9-]+$/', $nama)) {
            return '';
        }

        $isi = self::$cache[$nama] ??= self::baca($nama);

        $isi = preg_replace_callback('/^<svg\b([^>]*)>/', function (array $cocok) use ($kelas) {
            $atribut = preg_replace('/\s(?:width|height|fill|class|aria-hidden|focusable)="[^"]*"/', '', $cocok[1]);

            return '<svg'.$atribut.' class="'.e($kelas).'" width="24" height="24" fill="currentColor" aria-hidden="true" focusable="false">';
        }, $isi, 1);

        return $isi ?? '';
    }

    private static function baca(string $nama): string
    {
        $dasar = base_path('node_modules/@phosphor-icons/core/assets/regular/');

        foreach ([$dasar.$nama.'.svg', $dasar.$nama.'-regular.svg'] as $jalur) {
            if (is_file($jalur)) {
                $isi = trim((string) file_get_contents($jalur));
                $isi = preg_replace('/^<\?xml[^>]*\?>\s*/', '', $isi);

                if (str_starts_with($isi, '<svg')) {
                    return $isi;
                }
            }
        }

        // Cadangan bila paket belum dipasang (npm install): titik kecil agar tata letak tidak rusak.
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><circle cx="128" cy="128" r="14"/></svg>';
    }
}
