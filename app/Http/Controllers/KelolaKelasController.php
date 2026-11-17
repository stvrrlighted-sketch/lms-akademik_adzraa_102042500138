<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\MemeriksaAksesKelas;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KelolaKelasController extends Controller
{
    use MemeriksaAksesKelas;

    public function indeks(Request $request): View
    {
        $daftarKelas = Kelas::dapatDikelolaOleh($request->user())
            ->with(['mataKuliah', 'tahunAjaran', 'dosen'])
            ->withCount('mahasiswa')
            ->orderByDesc('tahun_ajaran_id')
            ->orderBy('id')
            ->get();

        return view('kelola.indeks', compact('daftarKelas'));
    }

    public function tampil(Request $request, Kelas $kelas): View
    {
        $kelas = $this->kelasKelola($request, $kelas->id);

        return view('kelola.tampil', [
            'kelas' => $kelas,
            'daftarMahasiswa' => $kelas->mahasiswa()->orderBy('name')->get(),
        ]);
    }
}
