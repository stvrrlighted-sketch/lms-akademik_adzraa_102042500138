<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\KuisPercobaan;
use App\Models\PengumpulanTugas;
use App\Models\User;
use Illuminate\Support\Collection;

class PerhitunganNilai
{
    public const BATAS_HURUF = [['A', 80], ['AB', 70], ['B', 65], ['BC', 60], ['C', 50], ['D', 40]];

    public function huruf(float $angka): string
    {
        foreach (self::BATAS_HURUF as [$huruf, $batas]) {
            if ($angka >= $batas) {
                return $huruf;
            }
        }

        return 'E';
    }

    public function rekapKelas(Kelas $kelas, ?int $idMahasiswa = null): Collection
    {
        $daftarMahasiswa = $kelas->mahasiswa()
            ->when($idMahasiswa, fn ($kueri) => $kueri->whereKey($idMahasiswa))
            ->orderBy('name')
            ->get();

        $idTugas = $kelas->tugas()->pluck('id');
        $idKuis = $kelas->kuis()->pluck('id');
        $jumlahItem = $idTugas->count() + $idKuis->count();

        $skorTugas = PengumpulanTugas::whereIn('tugas_id', $idTugas)->get()->groupBy('mahasiswa_id');
        $skorKuis = KuisPercobaan::whereIn('kuis_id', $idKuis)->get()->groupBy('mahasiswa_id');
        $nilaiKelas = $kelas->nilaiKelas()->get()->keyBy('mahasiswa_id');
        $jumlahBobot = max(1, $kelas->bobot_tugas + $kelas->bobot_uts + $kelas->bobot_uas);

        return $daftarMahasiswa->map(function (User $mahasiswa) use ($skorTugas, $skorKuis, $nilaiKelas, $jumlahItem, $jumlahBobot, $kelas) {
            $totalSkor = $skorTugas->get($mahasiswa->id, collect())->sum('skor')
                + $skorKuis->get($mahasiswa->id, collect())->sum('skor');

            $tugas = $jumlahItem > 0 ? $totalSkor / $jumlahItem : null;
            $nilai = $nilaiKelas->get($mahasiswa->id);
            $uts = $nilai?->uts;
            $uas = $nilai?->uas;

            $akhir = round((
                ($tugas ?? 0) * $kelas->bobot_tugas
                + ($uts ?? 0) * $kelas->bobot_uts
                + ($uas ?? 0) * $kelas->bobot_uas
            ) / $jumlahBobot, 2);

            return [
                'mahasiswa' => $mahasiswa,
                'tugas' => $tugas === null ? null : round($tugas, 2),
                'uts' => $uts,
                'uas' => $uas,
                'akhir' => $akhir,
                'huruf' => $this->huruf($akhir),
                'umpan_balik' => $nilai?->umpan_balik,
            ];
        })->values();
    }
}
