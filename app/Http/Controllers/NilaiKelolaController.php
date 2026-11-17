<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\MemeriksaAksesKelas;
use App\Http\Controllers\Concerns\MengeksporCsv;
use App\Models\Kelas;
use App\Models\NilaiKelas;
use App\Services\PerhitunganNilai;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NilaiKelolaController extends Controller
{
    use MemeriksaAksesKelas;
    use MengeksporCsv;

    public function indeks(Request $request, Kelas $kelas, PerhitunganNilai $hitung): View
    {
        $kelas = $this->kelasKelola($request, $kelas->id);

        return view('kelola.nilai', [
            'kelas' => $kelas,
            'daftarRekap' => $hitung->rekapKelas($kelas),
        ]);
    }

    public function bobot(Request $request, Kelas $kelas): RedirectResponse
    {
        $kelas = $this->kelasKelola($request, $kelas->id);

        $data = $request->validate([
            'bobot_tugas' => ['required', 'integer', 'between:0,100'],
            'bobot_uts' => ['required', 'integer', 'between:0,100'],
            'bobot_uas' => ['required', 'integer', 'between:0,100'],
        ]);

        if (array_sum($data) !== 100) {
            return back()->withErrors(['bobot_tugas' => 'Total bobot harus tepat 100.'])->withInput();
        }

        $kelas->update($data);

        return back()->with('sukses', 'Bobot nilai disimpan.');
    }

    public function simpan(Request $request, Kelas $kelas): RedirectResponse
    {
        $kelas = $this->kelasKelola($request, $kelas->id);

        $data = $request->validate([
            'nilai' => ['nullable', 'array'],
            'nilai.*.uts' => ['nullable', 'integer', 'between:0,100'],
            'nilai.*.uas' => ['nullable', 'integer', 'between:0,100'],
            'nilai.*.umpan_balik' => ['nullable', 'string', 'max:1000'],
        ], [], [
            'nilai.*.uts' => __('UTS'),
            'nilai.*.uas' => __('UAS'),
            'nilai.*.umpan_balik' => __('Umpan balik'),
        ]);

        // Hanya mahasiswa yang terdaftar di kelas ini yang diproses; id dari formulir tidak dipercaya.
        foreach ($kelas->mahasiswa()->pluck('users.id') as $idMahasiswa) {
            $isi = $data['nilai'][$idMahasiswa] ?? null;

            if ($isi === null) {
                continue;
            }

            NilaiKelas::updateOrCreate(
                ['kelas_id' => $kelas->id, 'mahasiswa_id' => $idMahasiswa],
                ['uts' => $isi['uts'] ?? null, 'uas' => $isi['uas'] ?? null, 'umpan_balik' => $isi['umpan_balik'] ?? null],
            );
        }

        return back()->with('sukses', 'Nilai UTS dan UAS disimpan.');
    }

    public function ekspor(Request $request, Kelas $kelas, PerhitunganNilai $hitung)
    {
        $kelas = $this->kelasKelola($request, $kelas->id);

        $baris = $hitung->rekapKelas($kelas)->map(fn ($r) => [
            $r['mahasiswa']->name,
            $r['mahasiswa']->email,
            $r['tugas'] ?? '',
            $r['uts'] ?? '',
            $r['uas'] ?? '',
            $r['akhir'],
            $r['huruf'],
            $r['umpan_balik'] ?? '',
        ])->all();

        return $this->unduhCsv(
            'rekap-nilai-'.Str::slug($kelas->label()).'.csv',
            ['Nama', 'Email', 'Rata-rata tugas dan kuis', 'UTS', 'UAS', 'Nilai akhir', 'Huruf', 'Umpan balik'],
            $baris,
        );
    }
}
