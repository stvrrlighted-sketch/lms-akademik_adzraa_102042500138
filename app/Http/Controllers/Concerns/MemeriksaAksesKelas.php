<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Kelas;
use Illuminate\Http\Request;

trait MemeriksaAksesKelas
{
    protected function kelasKelola(Request $request, int $idKelas): Kelas
    {
        // 404, bukan 403, agar keberadaan kelas milik dosen lain tidak terungkap.
        return Kelas::dapatDikelolaOleh($request->user())
            ->with(['mataKuliah', 'tahunAjaran', 'dosen'])
            ->findOrFail($idKelas);
    }

    protected function kelasIkuti(Request $request, int $idKelas): Kelas
    {
        return Kelas::dapatDiikutiOleh($request->user())
            ->with(['mataKuliah', 'tahunAjaran', 'dosen'])
            ->findOrFail($idKelas);
    }
}
