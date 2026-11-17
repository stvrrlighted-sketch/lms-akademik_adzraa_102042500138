<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\MemeriksaAksesKelas;
use App\Models\Kelas;
use App\Models\Tugas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TugasKelolaController extends Controller
{
    use MemeriksaAksesKelas;

    public function indeks(Request $request, Kelas $kelas): View
    {
        $kelas = $this->kelasKelola($request, $kelas->id);

        return view('kelola.tugas-indeks', [
            'kelas' => $kelas,
            'daftarTugas' => $kelas->tugas()->withCount('pengumpulan')->orderBy('tenggat')->get(),
            'jumlahMahasiswa' => $kelas->mahasiswa()->count(),
        ]);
    }

    public function buat(Request $request, Kelas $kelas): View
    {
        $kelas = $this->kelasKelola($request, $kelas->id);

        return view('kelola.tugas-form', ['kelas' => $kelas, 'tugas' => new Tugas]);
    }

    public function simpan(Request $request, Kelas $kelas): RedirectResponse
    {
        $kelas = $this->kelasKelola($request, $kelas->id);

        $kelas->tugas()->create($request->validate($this->aturan()));

        return redirect()->route('tugas.indeks', $kelas)->with('sukses', 'Tugas dibuat.');
    }

    public function tampil(Request $request, Tugas $tugas): View
    {
        $kelas = $this->kelasKelola($request, $tugas->kelas_id);

        return view('kelola.tugas-tampil', [
            'kelas' => $kelas,
            'tugas' => $tugas,
            'daftarMahasiswa' => $kelas->mahasiswa()->orderBy('name')->get(),
            'pengumpulan' => $tugas->pengumpulan()->get()->keyBy('mahasiswa_id'),
        ]);
    }

    public function ubah(Request $request, Tugas $tugas): View
    {
        $kelas = $this->kelasKelola($request, $tugas->kelas_id);

        return view('kelola.tugas-form', compact('kelas', 'tugas'));
    }

    public function perbarui(Request $request, Tugas $tugas): RedirectResponse
    {
        $kelas = $this->kelasKelola($request, $tugas->kelas_id);

        $tugas->update($request->validate($this->aturan()));

        return redirect()->route('tugas.indeks', $kelas)->with('sukses', 'Tugas diperbarui.');
    }

    public function hapus(Request $request, Tugas $tugas): RedirectResponse
    {
        $kelas = $this->kelasKelola($request, $tugas->kelas_id);

        Storage::disk('local')->delete($tugas->pengumpulan()->pluck('path')->all());
        $tugas->delete();

        return redirect()->route('tugas.indeks', $kelas)->with('sukses', 'Tugas dihapus beserta pengumpulannya.');
    }

    public function nilai(Request $request, Tugas $tugas): RedirectResponse
    {
        $this->kelasKelola($request, $tugas->kelas_id);

        $data = $request->validate([
            'nilai' => ['nullable', 'array'],
            'nilai.*.skor' => ['nullable', 'integer', 'between:0,100'],
            'nilai.*.umpan_balik' => ['nullable', 'string', 'max:1000'],
        ], [], [
            'nilai.*.skor' => __('Skor'),
            'nilai.*.umpan_balik' => __('Umpan balik'),
        ]);

        foreach ($data['nilai'] ?? [] as $idPengumpulan => $isi) {
            // Hanya pengumpulan milik tugas ini yang diubah; id dari formulir tidak dipercaya.
            $pengumpulan = $tugas->pengumpulan()->whereKey($idPengumpulan)->first();

            $pengumpulan?->update([
                'skor' => $isi['skor'] ?? null,
                'umpan_balik' => $isi['umpan_balik'] ?? null,
            ]);
        }

        return back()->with('sukses', 'Nilai tugas disimpan.');
    }

    private function aturan(): array
    {
        return [
            'judul' => ['required', 'string', 'max:150'],
            'instruksi' => ['required', 'string', 'max:5000'],
            'tenggat' => ['required', 'date'],
        ];
    }
}
