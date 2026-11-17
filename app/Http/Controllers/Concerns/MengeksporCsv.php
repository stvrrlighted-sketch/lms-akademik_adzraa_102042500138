<?php

namespace App\Http\Controllers\Concerns;

use Symfony\Component\HttpFoundation\StreamedResponse;

trait MengeksporCsv
{
    protected function unduhCsv(string $namaBerkas, array $judul, array $daftarBaris): StreamedResponse
    {
        return response()->streamDownload(function () use ($judul, $daftarBaris) {
            $keluaran = fopen('php://output', 'w');
            fwrite($keluaran, "\xEF\xBB\xBF");
            fputcsv($keluaran, $judul, ',', '"', '');

            foreach ($daftarBaris as $baris) {
                fputcsv($keluaran, array_map(fn ($nilai) => $this->amankanSel($nilai), $baris), ',', '"', '');
            }

            fclose($keluaran);
        }, $namaBerkas, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function amankanSel(mixed $nilai): string
    {
        $teks = (string) $nilai;

        // Sel yang diawali = + - @ dapat dieksekusi sebagai rumus di Excel; awalan tanda kutip mencegahnya.
        return preg_match('/^[=+\-@\t\r]/', $teks) ? "'".$teks : $teks;
    }
}
