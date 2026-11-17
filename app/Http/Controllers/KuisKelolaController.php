<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\MemeriksaAksesKelas;
use App\Models\Kelas;
use App\Models\Kuis;
use App\Models\KuisSoal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KuisKelolaController extends Controller
{
    use MemeriksaAksesKelas;

    public function indeks(Request $request, Kelas $kelas): View
    {
        $kelas = $this->kelasKelola($request, $kelas->id);

        return view('kelola.kuis-indeks', [
            'kelas' => $kelas,
            'daftarKuis' => $kelas->kuis()->withCount(['soal', 'percobaan'])->orderBy('id')->get(),
        ]);
    }

    public function simpan(Request $request, Kelas $kelas): RedirectResponse
    {
        $kelas = $this->kelasKelola($request, $kelas->id);

        $data = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'tenggat' => ['nullable', 'date'],
        ]);

        $kuis = $kelas->kuis()->create($data);

        return redirect()->route('kuis.tampil', $kuis)->with('sukses', 'Kuis dibuat. Tambahkan soal di bawah.');
    }

    public function tampil(Request $request, Kuis $kuis): View
    {
        $kelas = $this->kelasKelola($request, $kuis->kelas_id);

        return view('kelola.kuis-tampil', [
            'kelas' => $kelas,
            'kuis' => $kuis,
            'daftarSoal' => $kuis->soal()->orderBy('id')->get(),
            'daftarPercobaan' => $kuis->percobaan()->with('mahasiswa')->orderBy('selesai_pada')->get(),
        ]);
    }

    public function hapus(Request $request, Kuis $kuis): RedirectResponse
    {
        $kelas = $this->kelasKelola($request, $kuis->kelas_id);

        $kuis->delete();

        return redirect()->route('kuis.indeks', $kelas)->with('sukses', 'Kuis dihapus.');
    }

    public function tambahSoal(Request $request, Kuis $kuis): RedirectResponse
    {
        $this->kelasKelola($request, $kuis->kelas_id);

        $data = $request->validate([
            'jenis' => ['required', Rule::in(['pilihan_ganda', 'esai'])],
            'pertanyaan' => ['required', 'string', 'max:1000'],
        ]);

        $soal = ['jenis' => $data['jenis'], 'pertanyaan' => $data['pertanyaan']];

        if ($data['jenis'] === 'pilihan_ganda') {
            $pilihan = $request->validate([
                'opsi.A' => ['required', 'string', 'max:255'],
                'opsi.B' => ['required', 'string', 'max:255'],
                'opsi.C' => ['required', 'string', 'max:255'],
                'opsi.D' => ['required', 'string', 'max:255'],
                'kunci' => ['required', Rule::in(['A', 'B', 'C', 'D'])],
            ], [], [
                'opsi.A' => __('Opsi :a', ['a' => 'A']),
                'opsi.B' => __('Opsi :a', ['a' => 'B']),
                'opsi.C' => __('Opsi :a', ['a' => 'C']),
                'opsi.D' => __('Opsi :a', ['a' => 'D']),
            ]);

            $soal['opsi'] = $pilihan['opsi'];
            $soal['kunci'] = $pilihan['kunci'];
        }

        $kuis->soal()->create($soal);

        return back()->with('sukses', 'Soal ditambahkan.');
    }

    public function hapusSoal(Request $request, KuisSoal $soal): RedirectResponse
    {
        $this->kelasKelola($request, $soal->kuis->kelas_id);

        $soal->delete();

        return back()->with('sukses', 'Soal dihapus.');
    }

    public function nilai(Request $request, Kuis $kuis): RedirectResponse
    {
        $this->kelasKelola($request, $kuis->kelas_id);

        $data = $request->validate([
            'percobaan' => ['nullable', 'array'],
            'percobaan.*.skor' => ['nullable', 'integer', 'between:0,100'],
            'percobaan.*.umpan_balik' => ['nullable', 'string', 'max:1000'],
        ], [], [
            'percobaan.*.skor' => __('Skor'),
            'percobaan.*.umpan_balik' => __('Umpan balik'),
        ]);

        foreach ($data['percobaan'] ?? [] as $idPercobaan => $isi) {
            // Hanya percobaan milik kuis ini yang diubah; id dari formulir tidak dipercaya.
            $percobaan = $kuis->percobaan()->whereKey($idPercobaan)->first();

            $percobaan?->update([
                'skor' => $isi['skor'] ?? null,
                'umpan_balik' => $isi['umpan_balik'] ?? null,
            ]);
        }

        return back()->with('sukses', 'Nilai kuis disimpan.');
    }
}
