<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Materi;
use App\Models\PengumpulanTugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UnduhController extends Controller
{
    public function materi(Request $request, Materi $materi)
    {
        abort_if($materi->jenis !== 'berkas' || ! $materi->path, 404);

        $idKelas = $materi->pertemuan->kelas_id;
        $pengguna = $request->user();

        // Berkas materi bersifat privat: hanya pengelola kelas dan mahasiswa yang terdaftar yang boleh mengunduh.
        $boleh = Kelas::dapatDikelolaOleh($pengguna)->whereKey($idKelas)->exists()
            || Kelas::dapatDiikutiOleh($pengguna)->whereKey($idKelas)->exists();

        abort_unless($boleh && Storage::disk('local')->exists($materi->path), 404);

        return Storage::disk('local')->download($materi->path, $materi->nama_asli);
    }

    public function pengumpulan(Request $request, PengumpulanTugas $pengumpulan)
    {
        $pengguna = $request->user();

        // Hanya pemilik berkas dan pengelola kelasnya yang boleh mengunduh pengumpulan tugas.
        $boleh = $pengumpulan->mahasiswa_id === $pengguna->id
            || Kelas::dapatDikelolaOleh($pengguna)->whereKey($pengumpulan->tugas->kelas_id)->exists();

        abort_unless($boleh && Storage::disk('local')->exists($pengumpulan->path), 404);

        return Storage::disk('local')->download($pengumpulan->path, $pengumpulan->nama_asli);
    }
}
