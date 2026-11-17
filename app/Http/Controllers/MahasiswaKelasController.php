<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\MemeriksaAksesKelas;
use App\Models\Kelas;
use App\Models\Kuis;
use App\Models\KuisPercobaan;
use App\Models\PengumpulanTugas;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use App\Models\Tugas;
use App\Services\PerhitunganNilai;
use App\Services\RekapPresensi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MahasiswaKelasController extends Controller
{
    use MemeriksaAksesKelas;

    public function indeks(Request $request): View
    {
        return view('mahasiswa.kelas-indeks', [
            'daftarKelas' => $request->user()->kelasDiikuti()->with(['mataKuliah', 'tahunAjaran', 'dosen'])->get(),
        ]);
    }

    public function tampil(Request $request, Kelas $kelas, PerhitunganNilai $nilai, RekapPresensi $rekap): View
    {
        $kelas = $this->kelasIkuti($request, $kelas->id);
        $idSaya = $request->user()->id;

        $daftarPertemuan = $kelas->pertemuan()->with(['materi', 'sesiPresensi'])->orderBy('nomor')->get();
        $idSesi = SesiPresensi::whereIn('pertemuan_id', $kelas->pertemuan()->select('id'))->select('id');
        $daftarTugas = $kelas->tugas()->orderBy('tenggat')->get();
        $daftarKuis = $kelas->kuis()->withCount('soal')->orderBy('id')->get();

        return view('mahasiswa.kelas-tampil', [
            'kelas' => $kelas,
            'daftarPertemuan' => $daftarPertemuan,
            'presensiSaya' => Presensi::where('mahasiswa_id', $idSaya)->whereIn('sesi_presensi_id', $idSesi)->get()->keyBy('sesi_presensi_id'),
            'daftarTugas' => $daftarTugas,
            'pengumpulanSaya' => PengumpulanTugas::where('mahasiswa_id', $idSaya)->whereIn('tugas_id', $daftarTugas->pluck('id'))->get()->keyBy('tugas_id'),
            'daftarKuis' => $daftarKuis,
            'percobaanSaya' => KuisPercobaan::where('mahasiswa_id', $idSaya)->whereIn('kuis_id', $daftarKuis->pluck('id'))->get()->keyBy('kuis_id'),
            'rekapNilai' => $nilai->rekapKelas($kelas, $idSaya)->first(),
            'rekapPresensi' => $rekap->untukKelas($kelas, $idSaya)->first(),
        ]);
    }

    public function tugas(Request $request, Tugas $tugas): View
    {
        $kelas = $this->kelasIkuti($request, $tugas->kelas_id);

        return view('mahasiswa.tugas', [
            'kelas' => $kelas,
            'tugas' => $tugas,
            'pengumpulan' => PengumpulanTugas::where('tugas_id', $tugas->id)->where('mahasiswa_id', $request->user()->id)->first(),
        ]);
    }

    public function kumpulkan(Request $request, Tugas $tugas): RedirectResponse
    {
        $this->kelasIkuti($request, $tugas->kelas_id);

        $data = $request->validate([
            'berkas' => ['required', 'file', 'max:10240', 'extensions:pdf,doc,docx,ppt,pptx,zip,txt'],
        ]);

        $idSaya = $request->user()->id;
        $pengumpulan = PengumpulanTugas::where('tugas_id', $tugas->id)->where('mahasiswa_id', $idSaya)->first();

        if ($pengumpulan && $pengumpulan->skor !== null) {
            return back()->with('galat', 'Tugas sudah dinilai dan tidak dapat dikumpulkan ulang.');
        }

        $berkas = $request->file('berkas');
        $jalur = Storage::disk('local')->putFile('pengumpulan', $berkas);

        if ($pengumpulan) {
            Storage::disk('local')->delete($pengumpulan->path);
        }

        PengumpulanTugas::updateOrCreate(
            ['tugas_id' => $tugas->id, 'mahasiswa_id' => $idSaya],
            [
                'path' => $jalur,
                'nama_asli' => $berkas->getClientOriginalName(),
                'dikumpulkan_pada' => now(),
                'status' => now()->greaterThan($tugas->tenggat) ? 'Terlambat' : 'Terkumpul',
            ],
        );

        return redirect()->route('kelassaya.tugas', $tugas)->with('sukses', 'Tugas dikumpulkan.');
    }

    public function kuis(Request $request, Kuis $kuis): View
    {
        $kelas = $this->kelasIkuti($request, $kuis->kelas_id);

        return view('mahasiswa.kuis', [
            'kelas' => $kelas,
            'kuis' => $kuis,
            'daftarSoal' => $kuis->soal()->orderBy('id')->get(),
            'percobaan' => KuisPercobaan::where('kuis_id', $kuis->id)->where('mahasiswa_id', $request->user()->id)->first(),
            'ditutup' => $kuis->tenggat !== null && now()->greaterThan($kuis->tenggat),
        ]);
    }

    public function kerjakan(Request $request, Kuis $kuis): RedirectResponse
    {
        $this->kelasIkuti($request, $kuis->kelas_id);
        $idSaya = $request->user()->id;

        if (KuisPercobaan::where('kuis_id', $kuis->id)->where('mahasiswa_id', $idSaya)->exists()) {
            return back()->with('galat', 'Anda sudah mengerjakan kuis ini.');
        }

        // Tenggat diperiksa di server, bukan hanya di tampilan.
        if ($kuis->tenggat !== null && now()->greaterThan($kuis->tenggat)) {
            return back()->with('galat', 'Kuis sudah ditutup.');
        }

        $daftarSoal = $kuis->soal()->get();

        if ($daftarSoal->isEmpty()) {
            return back()->with('galat', 'Kuis belum memiliki soal.');
        }

        $jawaban = [];
        $benar = 0;
        $jumlahPilihan = 0;
        $adaEsai = false;

        foreach ($daftarSoal as $soal) {
            $masukan = $request->input('jawaban.'.$soal->id);

            if ($soal->jenis === 'pilihan_ganda') {
                $jumlahPilihan++;
                $dipilih = is_string($masukan) && in_array($masukan, ['A', 'B', 'C', 'D'], true) ? $masukan : null;
                $jawaban[$soal->id] = $dipilih;

                if ($dipilih !== null && $dipilih === $soal->kunci) {
                    $benar++;
                }
            } else {
                $adaEsai = true;
                $jawaban[$soal->id] = is_string($masukan) ? mb_substr(trim($masukan), 0, 2000) : '';
            }
        }

        KuisPercobaan::create([
            'kuis_id' => $kuis->id,
            'mahasiswa_id' => $idSaya,
            'jawaban' => $jawaban,
            'skor' => $adaEsai ? null : (int) round($benar / max(1, $jumlahPilihan) * 100),
            'selesai_pada' => now(),
        ]);

        return redirect()->route('kelassaya.kuis', $kuis)->with('sukses', 'Jawaban terkirim.');
    }

    public function hadir(Request $request, SesiPresensi $sesi): RedirectResponse
    {
        $this->kelasIkuti($request, $sesi->pertemuan->kelas_id);

        // Check-in hanya sah saat sesi sedang dibuka dosen; pemeriksaan ini harus di server.
        if (! $sesi->dibuka) {
            return back()->with('galat', 'Sesi presensi sedang tidak dibuka.');
        }

        Presensi::updateOrCreate(
            ['sesi_presensi_id' => $sesi->id, 'mahasiswa_id' => $request->user()->id],
            ['status' => 'Hadir'],
        );

        return back()->with('sukses', 'Kehadiran Anda tercatat.');
    }
}
