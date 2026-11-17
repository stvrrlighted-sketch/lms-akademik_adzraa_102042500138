<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\MemeriksaAksesKelas;
use App\Http\Controllers\Concerns\MengeksporCsv;
use App\Models\Kelas;
use App\Models\Pertemuan;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use App\Services\RekapPresensi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PresensiKelolaController extends Controller
{
    use MemeriksaAksesKelas;
    use MengeksporCsv;

    public function indeks(Request $request, Kelas $kelas, RekapPresensi $rekap): View
    {
        $kelas = $this->kelasKelola($request, $kelas->id);

        foreach (range(1, 14) as $nomor) {
            Pertemuan::firstOrCreate(['kelas_id' => $kelas->id, 'nomor' => $nomor]);
        }

        return view('kelola.presensi-indeks', [
            'kelas' => $kelas,
            'daftarPertemuan' => $kelas->pertemuan()->with('sesiPresensi')->orderBy('nomor')->get(),
            'daftarRekap' => $rekap->untukKelas($kelas),
        ]);
    }

    public function buka(Request $request, Pertemuan $pertemuan): RedirectResponse
    {
        $this->kelasKelola($request, $pertemuan->kelas_id);

        $sesi = SesiPresensi::firstOrCreate(['pertemuan_id' => $pertemuan->id], ['dibuka' => false]);
        $sesi->update(['dibuka' => true]);

        return back()->with('sukses', 'Presensi pertemuan '.$pertemuan->nomor.' dibuka.');
    }

    public function tutup(Request $request, Pertemuan $pertemuan): RedirectResponse
    {
        $this->kelasKelola($request, $pertemuan->kelas_id);

        SesiPresensi::where('pertemuan_id', $pertemuan->id)->update(['dibuka' => false]);

        return back()->with('sukses', 'Presensi pertemuan '.$pertemuan->nomor.' ditutup.');
    }

    public function sesi(Request $request, Pertemuan $pertemuan): View
    {
        $kelas = $this->kelasKelola($request, $pertemuan->kelas_id);
        $sesi = SesiPresensi::firstOrCreate(['pertemuan_id' => $pertemuan->id], ['dibuka' => false]);

        return view('kelola.presensi-sesi', [
            'kelas' => $kelas,
            'pertemuan' => $pertemuan,
            'sesi' => $sesi,
            'daftarMahasiswa' => $kelas->mahasiswa()->orderBy('name')->get(),
            'catatan' => $sesi->presensi()->get()->keyBy('mahasiswa_id'),
        ]);
    }

    public function simpan(Request $request, Pertemuan $pertemuan): RedirectResponse
    {
        $kelas = $this->kelasKelola($request, $pertemuan->kelas_id);
        $sesi = SesiPresensi::firstOrCreate(['pertemuan_id' => $pertemuan->id], ['dibuka' => false]);

        $data = $request->validate([
            'status' => ['required', 'array'],
            'status.*' => ['required', Rule::in(Presensi::DAFTAR_STATUS)],
        ], [], ['status.*' => __('Status')]);

        // Hanya mahasiswa yang terdaftar di kelas ini yang dicatat; id dari formulir tidak dipercaya.
        foreach ($kelas->mahasiswa()->pluck('users.id') as $idMahasiswa) {
            if (isset($data['status'][$idMahasiswa])) {
                Presensi::updateOrCreate(
                    ['sesi_presensi_id' => $sesi->id, 'mahasiswa_id' => $idMahasiswa],
                    ['status' => $data['status'][$idMahasiswa]],
                );
            }
        }

        return back()->with('sukses', 'Kehadiran pertemuan '.$pertemuan->nomor.' disimpan.');
    }

    public function ekspor(Request $request, Kelas $kelas, RekapPresensi $rekap)
    {
        $kelas = $this->kelasKelola($request, $kelas->id);

        $baris = $rekap->untukKelas($kelas)->map(fn ($r) => [
            $r['mahasiswa']->name,
            $r['mahasiswa']->email,
            $r['hadir'],
            $r['izin'],
            $r['sakit'],
            $r['alpa'],
            $r['total'],
            $r['persen'] ?? '',
        ])->all();

        return $this->unduhCsv(
            'rekap-presensi-'.Str::slug($kelas->label()).'.csv',
            ['Nama', 'Email', 'Hadir', 'Izin', 'Sakit', 'Alpa', 'Sesi tercatat', 'Persentase hadir (%)'],
            $baris,
        );
    }
}
