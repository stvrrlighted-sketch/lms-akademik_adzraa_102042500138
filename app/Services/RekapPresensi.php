<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use App\Models\User;
use Illuminate\Support\Collection;

class RekapPresensi
{
    public function untukKelas(Kelas $kelas, ?int $idMahasiswa = null): Collection
    {
        $idSesi = SesiPresensi::whereIn('pertemuan_id', $kelas->pertemuan()->select('id'))->pluck('id');
        $totalSesi = $idSesi->count();
        $catatan = Presensi::whereIn('sesi_presensi_id', $idSesi)->get()->groupBy('mahasiswa_id');

        $daftarMahasiswa = $kelas->mahasiswa()
            ->when($idMahasiswa, fn ($kueri) => $kueri->whereKey($idMahasiswa))
            ->orderBy('name')
            ->get();

        return $daftarMahasiswa->map(function (User $mahasiswa) use ($catatan, $totalSesi) {
            $milik = $catatan->get($mahasiswa->id, collect());
            $hadir = $milik->where('status', 'Hadir')->count();
            $izin = $milik->where('status', 'Izin')->count();
            $sakit = $milik->where('status', 'Sakit')->count();

            return [
                'mahasiswa' => $mahasiswa,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => max(0, $totalSesi - $hadir - $izin - $sakit),
                'total' => $totalSesi,
                'persen' => $totalSesi > 0 ? round($hadir / $totalSesi * 100, 1) : null,
            ];
        })->values();
    }
}
